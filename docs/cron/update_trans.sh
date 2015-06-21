#!/bin/bash
set -e

ROOT='/var/www-dev'
"$ROOT""/docs/update-translations.py" -o $ROOT >/dev/null