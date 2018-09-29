#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
ROOT='/var/www-prod'

mysql -u "$DB_USER" -p"$DB_PASS" "$DB" < "$ROOT""/docs/database/scripts/wwwjdic.sql"

mv /var/tmp/*.csv "/var/www-downloads/exports"