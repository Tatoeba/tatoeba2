#!/bin/bash
set -e

/var/www-prod/bin/cake sphinx_indexes merge >/dev/null 2>&1
