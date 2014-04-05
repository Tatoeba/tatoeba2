#!/bin/bash

apt-get install -y git cmake g++ libevent-dev libexpat1-dev libgmm++-dev libmecab-dev mecab-naist-jdic

git clone https://github.com/allan-simon/nihongoparserd.git

cd nihongoparserd
mkdir build
cd build
cmake ..
make
ln -s $(pwd)/nihongoparserd /usr/local/bin/nihongoparserd

cp ../conf/nihongoparserd /etc/init.d/
chmod +x /etc/init.d/nihongoparserd

cp ../conf/default /etc/default/nihongoparserd

useradd -r nihongoparserd
/etc/init.d/nihongoparserd start
update-rc.d nihongoparserd defaults
