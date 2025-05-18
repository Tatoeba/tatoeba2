#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
HOME='/home/debian'

if [[ -f "$HOME/dump/db.gz" ]]; then
    mv "$HOME/dump/db.gz" "$HOME/dump/db_old.gz"
fi

mysqldump --single-transaction -u "$DB_USER" -p"$DB_PASS" "$DB" | gzip > "$HOME""/dump/db.gz"
