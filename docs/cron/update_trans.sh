#!/bin/bash
set -e

ROOT='/srv/tatoeba.org/www'
cd "$ROOT"
./tools/update-translations.sh -a
