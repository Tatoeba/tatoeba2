#!/bin/bash
set -e

PATH=$PATH:/usr/local/mysql/bin
ROOT='/var/www-prod'

mysql -u "$DB_USER" -p"$DB_PASS" "$DB" < $ROOT/docs/database/scripts/weekly_exports.sql

mv /var/tmp/*csv "$DL_DIR"

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

# Create per-language files for the different sentences files
TEMP_DIR='/var/tmp/per_language'
trap "rm -rf $TEMP_DIR" EXIT

echo 'select code from languages;' |
( mysql --skip-column-names tatoeba; echo 'unknown' ) |
xargs -L 1 -I @ mkdir -p $TEMP_DIR/@

split_file () {
    local BASENAME=_${1%csv}tsv
    local DIR=${TEMP_DIR}/

    awk -F"\t" -v basename=$BASENAME -v dir=$DIR \
    '{print >> ($2 == "\\N" ? dir "unknown/unknown" basename : dir $2 "/" $2 basename)}' \
    $1
}

split_file sentences_detailed.csv
split_file sentences.csv
split_file sentences_CC0.csv
find $TEMP_DIR -path '*tsv' -exec bzip2 -qf '{}' +
rm -rf ${DL_DIR}per_language
mv -f $TEMP_DIR $DL_DIR
