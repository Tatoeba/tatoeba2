#!/bin/bash

#Install dependencies
sudo apt-get install -y git cmake g++ libevent-dev libexpat1-dev libgmm++-dev

#Grab the source from the repo
git clone https://github.com/allan-simon/sinoparserd.git

#Generate the makefile and build the source
cd sinoparserd
mkdir build
cd build
cmake ..
make

#Copy the binary to a system-wide location
sudo cp sinoparserd /usr/local/bin/sinoparserd

#Prepeare the init script and copy it to system's init script location
touch init.d
echo -e '
#! /bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON=/usr/local/bin/sinoparserd
NAME=sinoparserd
DESC=sinoparserd
USER=sinoparserd

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
    start-stop-daemon --background --make-pidfile --start --quiet --pidfile /var/run/$NAME.pid -c $USER --exec $DAEMON -- $DAEMON_OPTS
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

sudo cp init.d /etc/init.d/sinoparserd
sudo chmod +x /etc/init.d/sinoparserd

#Prepare default options file and copy it to a system-wide location
touch default
echo '
DAEMON_OPTS=" -c /etc/cantonese.xml -m /etc/mandarin.xml -h 127.0.0.1 -p 8042"
' > default
sudo cp default /etc/default/sinoparserd

#Copy dictionary files to a system-wide location
cd ..
sudo cp doc/cantonese.xml /etc/cantonese.xml
sudo cp doc/mandarin.xml /etc/mandarin.xml

#Add an unprivileged user
sudo useradd -r sinoparserd
#Start the daemon
/etc/init.d/sinoparserd start
#Add the init script to system startup
sudo update-rc.d sinoparserd defaults
