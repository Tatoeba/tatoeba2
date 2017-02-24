#!/bin/bash
set -e

/usr/bin/time -p -a -o /tmp/sphinx.main.update.log /var/www-prod/app/Console/cake sphinx_indexes update main >/dev/null 2>&1