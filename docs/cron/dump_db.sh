#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
HOME='/home/debian'

tar -zcf "$HOME""/dump/db_old.tar.gz" "$HOME""/dump/db.sql"
mysqldump -u "$DB_USER" -p"$DB_PASS" --routines --triggers "$DB" > "$HOME""/dump/db.sql"