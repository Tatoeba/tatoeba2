#!/bin/bash
set -e

PIDFILE=/var/run/sphinxsearch/searchd.pid

systemctl show -p MainPID --value sphinxsearch > $PIDFILE
/var/www-prod/bin/cake sphinx_indexes merge >/dev/null 2>&1
