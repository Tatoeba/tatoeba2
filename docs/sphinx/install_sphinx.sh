#!/bin/bash

#run this script as su

set -e -u

#This script installs Sphinx-search_2.1.6-release-1 on a Wheezy (Debian) i386 machine
#For a different version/machine change the variable "sphinx_version" appropriately
sphinx_version="sphinxsearch_2.1.6-release-1~wheezy_i386.deb"

#Install dependencies
apt-get install -y mysql-client unixodbc libpq5

#Fetch the .deb package
wget http://sphinxsearch.com/files/$sphinx_version

#Install Sphinx Sphinx-search
dpkg --install $sphinx_version

#Remove the .deb package
rm $sphinx_version
