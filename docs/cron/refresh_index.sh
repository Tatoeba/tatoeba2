#!/bin/bash
set -e

/usr/bin/time -p -a -o /tmp/sphinx.main.update.log /var/www-prod/cake/console/cake -app /var/www-prod/app sphinx_indexes update main >/dev/null 2>&1