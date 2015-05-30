#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
USER='tatouser'
ROOT='/var/www-prod'
PASSWORD=$(grep 'password' $ROOT'/app/config/database.php' | head -n 1 | sed "s/^.*=> '//; s/',$//")

mysql -u $USER -p $PASSWORD tatoeba_prod -e "call wwwjdic_issues_fix"
mysql -u $USER -p $PASSWORD tatoeba_prod < /var/www-prod/docs/database/scripts/wwwjdic.sql

mv /var/tmp/*.csv /var/www-prod/app/webroot/files/downloads