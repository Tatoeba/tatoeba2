#!/bin/bash

# Set mysql user and password
user=''
pass=''

# Backup all databases
mkdir ~/backup
mysqldump -u "$user" -p"$pass" -A > ~/backup/all_dbs.sql

# Generate Innodb conversion script
touch ToInnodb.sql
echo "SET SQL_LOG_BIN = 0;" > ToInnodb.sql
mysql -u "$user" -p"$pass" --skip-column-names -A -e "SELECT CONCAT('ALTER TABLE ',table_schema,'.',table_name,' ENGINE=InnoDB;') FROM information_schema.tables WHERE engine = 'MyISAM' AND table_schema NOT IN ('information_schema','mysql','performance_schema')" >> ToInnodb.sql

# Convert all myisam tables to innodb
mysql -u "$user" -p"$pass" < ToInnodb.sql

# Remove and purge mysql packages and config files
apt-get remove --purge -y mysql-server libmysqlclient18
mv /etc/mysql/my.cnf /etc/mysql/my.cnf.bk

# Add mariadb repos and keys
apt-get install -y python-software-properties
apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xcbcb082a1bb943db
add-apt-repository 'deb http://mirror.stshosting.co.uk/mariadb/repo/10.0/debian wheezy main'

# Install mariadb
apt-get install -y 'libmysqlclient18=10.0.10+maria-1~wheezy' 'mysql-common=10.0.10+maria-1~wheezy'
apt-get install -y mariadb-server
