#!/bin/bash
set -e

TATODETECT_USER=tatodetect
NGRAMS_DB=/etc/ngrams.db
NGRAMS_DB_TMP=/tmp/ngrams.db.new
SENTENCES_DETAILED_CSV=/var/www-downloads/exports/sentences_detailed.csv
TAGS_CSV=/var/www-downloads/exports/tags.csv

sudo -u $TATODETECT_USER nice -n 19 tatodetect-generate-ngrams.py "$SENTENCES_DETAILED_CSV" "$NGRAMS_DB_TMP" "$TAGS_CSV"
chown root:root "$NGRAMS_DB_TMP"
mv "$NGRAMS_DB_TMP" "$NGRAMS_DB"
systemctl restart tatodetect
