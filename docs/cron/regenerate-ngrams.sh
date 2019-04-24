#!/bin/bash
set -e

NGRAMS_DB=/etc/ngrams.db
NGRAMS_DB_TMP=/etc/ngrams.db.new
SENTENCES_DETAILED_CSV=/var/www-downloads/exports/sentences_detailed.csv
TAGS_CSV=/var/www-downloads/exports/tags.csv

nice -n 19 tatodetect-generate-ngrams.py "$SENTENCES_DETAILED_CSV" "$NGRAMS_DB_TMP" "$TAGS_CSV"
mv "$NGRAMS_DB_TMP" "$NGRAMS_DB"
/etc/init.d/tatodetect restart
