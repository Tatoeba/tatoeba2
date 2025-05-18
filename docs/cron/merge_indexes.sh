#!/bin/bash
set -e

echo "Started at $(date -Iseconds)"
/usr/bin/time -f "Duration: %E" /var/www-prod/bin/cake sphinx_indexes merge
echo -e "Finished at $(date -Iseconds)\n"
