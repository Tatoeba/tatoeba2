#!/bin/bash

mysql_user="root"
mysql_passwd="tatoeba"
mysql_db="tatoeba"

apt-get install -y gcc automake make libevent-dev libsqlite3-dev pkg-config libexpat1-dev libmysqlclient-dev

git clone https://github.com/allan-simon/suggestd.git

cd suggestd

aclocal
autoconf
automake --add-missing
./configure
make
make install
touch suggestd
echo -e '
#! /bin/sh\n
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin\n
DAEMON=/usr/local/bin/suggestd\n
NAME=suggestd\n
DESC=suggestd\n
USER=suggestd\n

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
' > suggestd

cp suggestd /etc/init.d/
chmod +x /etc/init.d/suggestd

touch default
echo "
DAEMON_OPTS=\"  --conf /etc/suggestd.xml\"
" > default
cp default /etc/default/suggestd

touch suggestd.xml
echo "
<suggestd>
    <param name=\"charset\" value=\"utf8\"/>
    <mysql host=\"localhost\" user=\"$mysql_user\" passwd=\"$mysql_passwd\" db=\"$mysql_db\">
        <query str=\"select name , nbrOfSentences from tags;\" />
    </mysql>
</suggestd>

" > suggestd.xml
cp suggestd.xml /etc/

useradd -r suggestd
/etc/init.d/suggestd start
update-rc.d suggestd defaults
