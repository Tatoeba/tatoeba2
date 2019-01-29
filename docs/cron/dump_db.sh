#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
HOME='/home/debian'

if [[ -f "$HOME/dump/db.tar.gz" ]]; then
    mv "$HOME/dump/db.tar.gz" "$HOME/dump/db_old.tar.gz"
fi

mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB" > "$HOME""/dump/db.sql"
tar -zcf "$HOME""/dump/db.tar.gz" "$HOME""/dump/db.sql"
rm "$HOME/dump/db.sql"
