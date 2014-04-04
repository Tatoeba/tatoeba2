#!/bin/bash

apt-get install -y git cmake g++ libevent-dev libexpat1-dev libgmm++-dev libglibmm-2.4-dev

git clone https://github.com/allan-simon/sinoparserd.git

sed -i 's/tree_str/TatoTreeStr/' sinoparserd/src/Index.h

cd sinoparserd
mkdir build
cd build
cmake ..
make
ln -s $(pwd)/sinoparserd /usr/local/bin/sinoparserd
chmod +x sinoparserd

touch init.d
echo -e '
#! /bin/sh\n
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin\n
DAEMON=/usr/local/bin/sinoparserd\n
NAME=sinoparserd\n
DESC=sinoparserd\n
USER=sinoparserd\n

test -x $DAEMON || exit 0\n

if [ -f /etc/default/$NAME ] ; then\n
    . /etc/default/$NAME\n
fi\n

set -e\n
\n
case "$1" in\n
  start-fg)\n
        $DAEMON $DAEMON_OPTS\n
    ;;\n
  start)\n
    echo -n "Starting $DESC: "\n
    start-stop-daemon --background --make-pidfile --start --quiet --pidfile /var/run/$NAME.pid -c $USER --exec $DAEMON -- $DAEMON_OPTS\n
    echo "$NAME."\n
    ;;\n
  stop)\n
    echo -n "Stopping $DESC: "\n
        PID=`cat /var/run/$NAME.pid\n`
    start-stop-daemon --stop --quiet --pidfile /var/run/$NAME.pid --exec $DAEMON\n
    echo "$NAME."\n
    ;;\n
  restart)\n
        echo -n "Restarting $DESC: "\n
    $0 stop\n
    sleep 1\n
    $0 start\n
    echo "$NAME."\n
    ;;\n
  *)\n
    N=/etc/init.d/$NAME\n
    echo "Usage: $N {start-fg|start|stop|restart}" >&2\n
    exit 1\n
    ;;\n
esac\n
\n
exit 0\n
' > init.d

cp init.d /etc/init.d/sinoparserd
chmod +x /etc/init.d/sinoparserd

touch default
echo "
DAEMON_OPTS=\" -c /etc/cantonese.xml -m /etc/mandarin.xml -h 127.0.0.1 -p 8042\"
" > default
cp default /etc/default/sinoparserd

cd ..
ln -s $(pwd)/doc/cantonese.xml /etc/cantonese.xml
ln -s $(pwd)/doc/mandarin.xml /etc/mandarin.xml

useradd -r sinoparserd
/etc/init.d/sinoparserd start
update-rc.d sinoparserd defaults
