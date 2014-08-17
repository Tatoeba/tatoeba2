#!/bin/bash

set -e -u

if [ "$#" -ne 2 ] ; then
	echo "Invalid number of arguments!"
	exit 1
fi

function get_all_versions {
	ls -1 -p "$1"/versions/ | grep /
}

function get_all_versions_count {
	get_all_versions | wc -l
}

function get_current_version {
	array=(`get_all_versions "$1"`)
	count="$2"
	current=(`readlink "$1""/versions/current"`)
	for dir in "${array[@]}"
	do
		if [ "$dir" = "$current" ] ; then
			break
		fi
		count=(`expr $count - 1`)
	done
	echo $count
}

repo_dir=$1 		#Path to directory where all old revisions are stored
version_count=(`get_all_versions_count "$repo_dir"`)
current_version=(`get_current_version "$repo_dir" "$version_count"`)
new_version=$2
versions=(`get_all_versions "$repo_dir"`)
counter="$version_count"

if [ $new_version -lt 1 ] || [ $new_version -gt $version_count ] ; then
	echo "Error! Invalid version number!"
	echo "There are $version_count revisions available in the code directory."
	echo "You are on revision $current_version."
	echo "Please choose a revision between 1 (latest) and $version_count (oldest) to switch to. Timestamps of versions are as follows:"
	for dir in "${versions[@]}"
	do
		echo "Version $counter: ""$dir"
		counter=(`expr $counter - 1`)
	done
	exit 1
fi

counter="$version_count"

for dir in "${versions[@]}"
do
	if [ "$counter" -eq "$new_version" ] ; then
		rm -rf "$repo_dir""/versions/current"
		ln -sf "$dir" "$repo_dir""/versions/current"
		break
	fi
	counter=(`expr $counter - 1`)
done

echo "Revision successfully changed to "$new_version""
exit 0