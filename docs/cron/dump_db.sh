#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
USER='tatouser'
PASSWORD=$(grep 'password' $ROOT'/app/config/database.php' | head -n 1 | sed "s/^.*=> '//; s/',$//")
HOME='/home/debian'

tar -zcf $HOME'/dump/db_old.tar.gz' $HOME'/dump/db.sql'
mysqldump -u $USER -p $PASSWORD --routines --triggers tatoeba_prod > $HOME'/dump/db.sql'