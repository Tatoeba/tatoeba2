#!/bin/bash

set -e -u

revision_limit="3" #Default number of old revisions to keep

if [ "$#" -lt 1 ] ; then
	exit 1
fi

if [ "$#" -ge 2 ] ; then
	if [ "$2" -ge 1 ]; then
		revision_limit="$2"
	fi
fi

function get_dir_count {
	ls -1 -p "$1"/versions | grep / | wc -l
}

function get_all_versions {
	ls -1 -p "$1"/versions/ | grep / | head -n -"$2"
}

repo_dir="$1" #Path to directory where all old revisions will be stored
temp_dir="$3" #Name of the temporary repo directory that is to be renamed

dir_count=(`get_dir_count "$1"`) #Number of revisions present currently

if [ "$dir_count" -ge "$revision_limit" ] ; then #If number of revisions exceed the limit, remove the old ones
	
	count=$((revision_limit - 1))
	array=(`get_all_versions "$repo_dir" "$count"`)
	
	for var in "${array[@]}"
	do
  		echo "Removing ${var}"
  		sudo rm -rf "$repo_dir""/versions/""$var"
	done
	
fi

#Create new directory for new revision
dir_name=$(date +%F--%H-%M-%S)
mv "$repo_dir""/versions/""$temp_dir" "$repo_dir""/versions/""$dir_name"
rm -rf "$repo_dir""/versions/current"
ln -sf "$dir_name""/" "$repo_dir""/versions/current"