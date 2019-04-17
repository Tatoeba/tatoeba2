#!/bin/bash
set -e

/usr/bin/time -p -a -o /tmp/sphinx.main.update.log /var/www-prod/bin/cake sphinx_indexes update main >/dev/null 2>&1
