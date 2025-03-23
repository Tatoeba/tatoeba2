#!/bin/bash
set -e

ROOT='/var/horus'
"$ROOT/manage.py" deduplicate -l /var/log/ -a dedup.log -i '3h ago' -bHorus -c -e
