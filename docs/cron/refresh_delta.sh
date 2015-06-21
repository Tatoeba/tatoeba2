#!/bin/bash
set -e

/usr/bin/time -p -a -o /tmp/sphinx.delta.update.log /var/www-prod/cake/console/cake -app /var/www-prod/app sphinx_indexes update delta >/dev/null 2>&1