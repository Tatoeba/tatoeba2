#!/bin/bash

#Set mysql auth info
mysql_user="root"
mysql_passwd="tatoeba"
mysql_db="tatoeba"

#Get dependencies
sudo apt-get install -y gcc automake make libevent-dev libsqlite3-dev pkg-config libexpat1-dev libmysqlclient-dev

#Grab the source from github
git clone https://github.com/allan-simon/suggestd.git

cd suggestd

#Generate the makefiles and compile 
aclocal
autoconf
automake --add-missing
./configure
make

#Install binary to a system-wide location
sudo make install

#Prepare init script and copy it to a system-wide location
touch init.d
echo -e '
#! /bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON=/usr/local/bin/suggestd
NAME=suggestd
DESC=suggestd
USER=suggestd

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

sudo cp init.d /etc/init.d/suggestd
sudo chmod +x /etc/init.d/suggestd

#Prepare options file and copy it to a system-wide location
touch default
echo '
DAEMON_OPTS=" --conf /etc/suggestd.xml"
' > default
sudo cp default /etc/default/suggestd

#Prepare config file and copy it to a system-wide location
touch suggestd.xml
echo "
<suggestd>
    <param name=\"charset\" value=\"utf8\"/>
    <mysql host=\"localhost\" user=\"$mysql_user\" passwd=\"$mysql_passwd\" db=\"$mysql_db\">
        <query str=\"select name , nbrOfSentences from tags;\" />
    </mysql>
</suggestd>

" > suggestd.xml
sudo cp suggestd.xml /etc/

#Add an unprivileged user
sudo useradd -r suggestd
#Start the daemon
sudo /etc/init.d/suggestd start
#Add the init script to system startup
sudo update-rc.d suggestd defaults
