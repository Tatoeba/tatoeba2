#!/bin/bash
set -e

/var/www-prod/cake/console/cake -app /var/www-prod/app sphinx_indexes merge >/dev/null 2>&1
