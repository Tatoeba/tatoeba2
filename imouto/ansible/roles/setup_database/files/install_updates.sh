#!/bin/bash

set -e -u

#Date of the schema file that was used to build the initial db
schema_date="2013-04-06"

if [ "$#" -lt 5 ] ; then
	exit 1
fi

function get_update_files {
	ls -1 "$1"
}

mysql_user="$1"
mysql_password="$2"
mysql_db="$3"
update_dir="$4"
status_file="$5""/.last_db_update"

files=(`get_update_files "$update_dir"`)

if [[ -f $status_file ]] ; then
	last_update=`cat "$status_file" `
	if [[ "$last_update" > "$schema_date" ]] ; then
		schema_date="$last_update"
	fi
fi

for file in "${files[@]}"
do
	if [[ "$file" > "$schema_date" ]] ; then
		echo 'Imported '$file''
		mysql -u "$mysql_user" -p"$mysql_password" "$mysql_db" < "$update_dir"/"$file"
	else
		echo 'Skipped '$file''
	fi
done

echo `ls -1 "$4" | tail -1` > "$status_file" #Update the .last_update file