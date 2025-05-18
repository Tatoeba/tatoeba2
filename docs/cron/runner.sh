#!/bin/bash

cmd="$1"
shift 1

DB_USER=$(php -r 'require "/var/www-prod/config/paths.php"; $config = include "/var/www-prod/config/app_local.php"; echo $config["Datasources"]["default"]["username"];')
DB_PASS=$(php -r 'require "/var/www-prod/config/paths.php"; $config = include "/var/www-prod/config/app_local.php"; echo $config["Datasources"]["default"]["password"];')
DB=$(php -r 'require "/var/www-prod/config/paths.php"; $config = include "/var/www-prod/config/app_local.php"; echo $config["Datasources"]["default"]["database"];')
DL_DIR=$(php -r 'require "/var/www-prod/config/paths.php"; $config = include "/var/www-prod/config/app_local.php"; echo $config["Downloads"]["path"];')

DB_PASS="$DB_PASS" DB_USER="$DB_USER" DB="$DB" DL_DIR="$DL_DIR" "$cmd" "$@"
