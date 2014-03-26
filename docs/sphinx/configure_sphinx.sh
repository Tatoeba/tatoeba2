#!/bin/bash

#run this script as su
#generate_sphinx_conf.php should be present in the current directory

set -e -u

#Path of directory for storing sphinx.conf file and search logs
config_dir=''

#Path of directory for storing the indexes
index_dir=''

log_dir="$config_dir""logs"

#Set MySQL connection options
mysql_user=''
mysql_pass=''
mysql_db=''
mysql_sock='/run/mysqld/mysqld.sock'

mkdir -p "$log_dir"
mkdir -p "$index_dir"

#Look for generate_sphinx_conf.php under /var
script=$( find /var -name 'generate_sphinx_conf.php')

#Copy and edit configuration options, in preparation for run
cp $script 'tmp_generate_sphinx_conf.php'
script=$(pwd)"/tmp_generate_sphinx_conf.php"
escaped_index_dir=$(echo "$index_dir" | sed -e 's/\//\\\//g')
escaped_mysql_sock=$(echo "$mysql_sock" | sed -e 's/\//\\\//g')
escaped_log_dir=$(echo "$log_dir" | sed -e 's/\//\\\//g')
sed -i "s/INDEXDIR/$escaped_index_dir/g" $script
sed -i "s/USER/$mysql_user/g" $script
sed -i "s/PASSWORD/$mysql_pass/g" $script
sed -i "s/DATABASE/$mysql_db/g" $script
sed -i "s/SOCKET/$escaped_mysql_sock/g" $script
sed -i "s/LOGDIR/$escaped_log_dir/g" $script


#generate the sphinx.conf file
php "$script" > "$config_dir""sphinx.conf"

#remove temp file
rm -f "$script"

#symlink the conf to default searchd path for daemon startup
rm -f "/etc/sphinxsearch/sphinx.conf"
ln -s "$config_dir""sphinx.conf" "/etc/sphinxsearch/sphinx.conf"

#create indexes
indexer --config "$config_dir""sphinx.conf" --all

#start the search daemon
searchd --config "$config_dir""sphinx.conf"
