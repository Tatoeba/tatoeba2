#!/bin/bash
set -e

echo "Started at $(date -Iseconds)"
/usr/bin/time -f "Duration: %E" /var/www-prod/bin/cake sphinx_indexes update delta
echo "Finished at $(date -Iseconds)\n"
