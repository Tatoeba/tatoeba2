#!/bin/bash

#Install dependencies
sudo apt-get install -y git cmake g++ libevent-dev libexpat1-dev libgmm++-dev libmecab-dev mecab-naist-jdic

#Grab source
git clone https://github.com/allan-simon/nihongoparserd.git

#Generate makefile and compile
cd nihongoparserd
mkdir build
cd build
cmake ..
make

#Copy binary to system-wide location
sudo cp nihongoparserd /usr/local/bin/nihongoparserd

#Copy init and default files to system-wide location
sudo cp ../conf/nihongoparserd /etc/init.d/
sudo chmod +x /etc/init.d/nihongoparserd
sudo cp ../conf/default /etc/default/nihongoparserd

sudo useradd -r nihongoparserd
sudo /etc/init.d/nihongoparserd start
sudo update-rc.d nihongoparserd defaults
