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
