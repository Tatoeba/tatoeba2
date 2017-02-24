#!/bin/bash
set -e

/var/www-prod/app/Console/cake sphinx_indexes merge >/dev/null 2>&1
