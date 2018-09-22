#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
ROOT='/var/www-prod'

mysql -u "$DB_USER" -p"$DB_PASS" "$DB" < /var/www-prod/docs/database/scripts/weekly_exports.sql

DL_DIR="$ROOT""/app/webroot/files/downloads/"
mv /var/tmp/* "$DL_DIR"

cd "$DL_DIR"
tar -cjf sentences_detailed.tar.bz2 sentences_detailed.csv
tar -cjf links.tar.bz2 links.csv
tar -cjf sentences.tar.bz2 sentences.csv
tar -cjf contributions.tar.bz2 contributions.csv
rm contributions.csv
tar -cjf comments.tar.bz2 sentence_comments.csv
rm sentence_comments.csv
tar -cjf wall.tar.bz2 wall_posts.csv
rm wall_posts.csv
tar -cjf tags.tar.bz2 tags.csv
tar -cjf user_lists.tar.bz2 user_lists.csv
tar -cjf sentences_in_lists.tar.bz2 sentences_in_lists.csv
tar -cjf jpn_indices.tar.bz2 jpn_indices.csv
tar -cjf sentences_with_audio.tar.bz2 sentences_with_audio.csv
tar -cjf user_languages.tar.bz2 user_languages.csv
tar -cjf tags_detailed.tar.bz2 tags_detailed.csv
tar -cjf sentences_CC0.tar.bz2 sentences_CC0.csv
