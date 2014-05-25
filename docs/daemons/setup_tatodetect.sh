#!/bin/bash
# For this daemon to work you need to build the n-gram db and put it in /etc/

#Install cppcms dependencies
sudo apt-get install -y cmake g++ libpcre3-dev zlib1g-dev libgcrypt11-dev libicu-dev python

#Grab cppcms source files
wget http://cznic.dl.sourceforge.net/project/cppcms/cppcms/1.0.4/cppcms-1.0.4.tar.bz2
tar -jxvf cppcms*

#Build the source and install it
cd cppcms*
mkdir build
cd build
cmake ..
make
sudo make install
cd

#Grab cppdb source files
wget http://kaz.dl.sourceforge.net/project/cppcms/cppdb/0.3.1/cppdb-0.3.1.tar.bz2
tar -jxvf cppdb*

#Build and install it
cd cppdb*
mkdir build
cd build
cmake ..
make
sudo make install
cd

#Install tatodetect dependencies
sudo apt-get install -y git libsqlite3-dev

#Grab the source from the repo
git clone https://github.com/allan-simon/Tatodetect.git

#Generate the makefile and build the source
cd Tatodetect
mkdir build
cd build
cmake ..
make

#Copy the binary to a system-wide location
sudo cp tatodetect /usr/local/bin/tatodetect

#Prepare the init script and copy it to system's init script location
touch init.d
echo -e '
#! /bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/local/lib
DAEMON=/usr/local/bin/tatodetect
NAME=tatodetect
DESC=tatodetect

test -x $DAEMON || exit 0

if [ -f /etc/default/$NAME ] ; then
. /etc/default/$NAME
fi

set -e

case "$1" in
start-fg)
$DAEMON $DAEMON_OPTS
;;
start)
echo -n "Starting $DESC: "
start-stop-daemon --background --make-pidfile --start --quiet --pidfile /var/run/$NAME.pid --exec $DAEMON -- $DAEMON_OPTS
echo "$NAME."
;;
stop)
echo -n "Stopping $DESC: "
PID=`cat /var/run/$NAME.pid`
start-stop-daemon --stop --quiet --pidfile /var/run/$NAME.pid --exec $DAEMON
echo "$NAME."
;;
restart)
echo -n "Restarting $DESC: "
$0 stop
sleep 1
$0 start
echo "$NAME."
;;
*)
N=/etc/init.d/$NAME
echo "Usage: $N {start-fg|start|stop|restart}" >&2
exit 1
;;
esac

exit 0
' > init.d

sudo cp init.d /etc/init.d/tatodetect
sudo chmod +x /etc/init.d/tatodetect

#Prepare the config file and copy it to a system-wide location
touch tatodetect.js
echo '
{
"service" : {
"api" : "http",
"port" : 4242
},

    "cache" : {
        "backend" : "thread_shared"
    },
"http" : {
"script_names" : ["/tatodetect"]
},
"localization" : {
"encoding" : "utf-8",
"messages" : {
"paths" : [ "../locale" ],
"domains" : [ "hello" ]
},
"locales" : [ "en_GB.UTF-8", "fr_FR.UTF-8" ]
},
    "session" : {
        "expire" : "renew",
        "timeout" : 604800,
        "location" : "server",
        "server" : {
            "storage" : "memory"
        }
    },
"tatodetect" : {
        "web" : "http://127.0.0.1:4242/",
        "interfacelangs" : [
            ["en" , "en_GB.UTF-8", "English"],
            ["fr" , "fr_FR.UTF-8", "Français"]
        ],
        "sqlite3" : {
            "path" : "/etc/ngrams.db"
        }
}
}
' > tatodetect.js
sudo cp tatodetect.js /etc/

#Prepare default options file and copy it to a system-wide location
touch default
echo '
DAEMON_OPTS=" -c /etc/tatodetect.js"
' > default
sudo cp default /etc/default/tatodetect

#Add an unprivileged user
#sudo useradd -r tatodetect

#Start the daemon
/etc/init.d/tatodetect start
#Add the init script to system startup
sudo update-rc.d tatodetect defaults
