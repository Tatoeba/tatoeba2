#!/bin/bash
set -e

ROOT='/var/www-prod/'
python "$ROOT""/docs/tatoeba2-django/manage.py" deduplicate -l /var/log/ -a dedup.log -i '45min ago' -bHorus -c -e