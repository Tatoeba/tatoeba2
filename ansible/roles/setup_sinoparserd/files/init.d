#! /bin/sh
### BEGIN INIT INFO
# Provides:          sinoparserd
# Required-Start:    $local_fs $remote_fs $network $syslog $named
# Required-Stop:     $local_fs $remote_fs $network $syslog $named
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Start/stop sinoparserd web server
### END INIT INFO


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