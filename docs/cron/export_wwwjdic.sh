#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
ROOT='/var/www-prod'

mysql -u "$DB_USER" -p "$DB_PASS" tatoeba_prod -e "call wwwjdic_issues_fix"
mysql -u "$DB_USER" -p "$DB_PASS" < "$ROOT""/docs/database/scripts/wwwjdic.sql"

mv /var/tmp/*.csv "$ROOT""/app/webroot/files/downloads"