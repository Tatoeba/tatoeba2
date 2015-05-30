#!/bin/bash
set -e

ROOT='/var/www-dev'
python3 $ROOT/docs/update-translations.py -o $ROOT >/dev/null