#!/bin/bash

set -e -u

revision_limit="3" #Default number of old revisions to keep

if [ "$#" -lt 1 ] ; then
	exit 1
fi

if [ "$#" -ge 2 ] ; then
	if [ "$2" -ge 1 ]; then
		revision_limit=$2
	fi
fi

repo_dir=$1 #Path to directory where all old revisions will be stored

dir_count=$(ls -1 -p $1/versions | grep / | wc -l) #Number of revisions present currently

if [ "$dir_count" -ge "$revision_limit" ] ; then #If number of revisions exceed the limit, remove the old ones
	
	count=$((revision_limit - 1))
	array=(`ls -1 -p $repo_dir/versions/ | grep / | head -n -$count`)
	
	for var in "${array[@]}"
	do
  		echo "Removing ${var}"
  		sudo rm -rf "$repo_dir""/versions/""$var"
	done
	
fi

#Create new directory for new revision
dir_name=$(date +%F--%H-%M-%S)
mkdir -p "$repo_dir""/versions/""$dir_name"
rm -rf "$repo_dir""/versions/current"
ln -sf "$dir_name" "$repo_dir""/versions/current"
