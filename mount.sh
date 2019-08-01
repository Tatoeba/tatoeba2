#!/bin/bash

set -e -u

PORT="2222"
HOST="127.0.0.1"
USER="vagrant"

function usage_instructions {
	echo "USAGE:" 
	echo "To mount: $0 -M <HOST-DIR-PATH> <GUEST-DIR-PATH>"
	echo "To unmount: $0 -U <HOST-DIR-PATH>"
	echo "Note: For mounting both <HOST-DIR-PATH> and <GUEST-DIR-PATH> must exist and <HOST-DIR-PATH> should be empty"
}

function check_sshfs {
	command -v sshfs >/dev/null 2>&1 || { echo >&2 "'sshfs' not found! Please install it first!"; exit 1; }
}

check_sshfs

if [ "$#" -eq 2 ] ; then
	if [ "$1" != "-U" ] && [ "$1" != "-u" ] ; then
		echo "Invalid arguments!"
		usage_instructions
		exit 1
	else
		HOST_DIR="$2"
		fusermount -u "$HOST_DIR"
		echo "Successfully unmounted at $HOST_DIR"
		exit 0
	fi
elif [ "$#" -eq 3 ] ; then
	if [ "$1" != "-M" ] && [ "$1" != "-m" ] ; then
		echo "Invalid arguments!"
		usage_instructions
		exit 1
	else
		HOST_DIR="$2"
		GUEST_DIR="$3"
		KEY_PATH=$(vagrant ssh-config | awk '/IdentityFile/ {print $2}')
		sshfs "$USER"@"$HOST":"$GUEST_DIR" "$HOST_DIR" \
			-p "$PORT" \
			-o LogLevel=FATAL \
			-o Compression=yes \
			-o IdentitiesOnly=yes \
			-o StrictHostKeyChecking=no \
			-o UserKnownHostsFile=/dev/null \
			-o IdentityFile="$KEY_PATH"
		echo "Successfully mounted $HOST:$PORT's $GUEST_DIR at $HOST_DIR"
		exit 0
	fi
else
	echo "Invalid number of arguments!"
	usage_instructions
	exit 1
fi
