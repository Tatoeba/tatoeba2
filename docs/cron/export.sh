#!/bin/bash

set -e

ROOT='/var/www-prod'

rm -f /var/tmp/*.csv
mkdir -p "$DL_DIR"

mysql -u "$DB_USER" -p"$DB_PASS" "$DB" < $ROOT/docs/database/scripts/weekly_exports.sql
mv /var/tmp/*csv "$DL_DIR"

mysql -u "$DB_USER" -p"$DB_PASS" "$DB" < "$ROOT"/docs/database/scripts/wwwjdic.sql
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
tar -cjf transcriptions.tar.bz2 transcriptions.csv

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
split_file transcriptions.csv

# split links by language pair
mysql --skip-column-names --batch tatoeba -e \
    "SELECT 
      COALESCE(sentence_lang, '\N'), 
      COALESCE(translation_lang, '\N'),
      sentence_id, 
      translation_id
     FROM sentences_translations" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      src_lg = ($1 == "\\N" ? "unknown" : $1);
      tgt_lg = ($2 == "\\N" ? "unknown" : $2);
      fpath = dir "/" src_lg "/" src_lg "-" tgt_lg "_links.tsv";
      print $3, $4 >> fpath;
      close(fpath)
  }'

# split user languages by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT
       COALESCE(ul.language_code, '\N'), 
       COALESCE(ul.level, '\N'), 
       COALESCE(u.username, '\N'), 
       ul.details
     FROM users_languages ul 
       LEFT JOIN users u ON ul.of_user_id = u.id
     ORDER BY ul.language_code ASC, ul.level DESC, u.username ASC" | \
  awk -F"\t" -v dir=$TEMP_DIR '{
      lg = ($1 == "\\N" ? "unknown" : $1);      
      fpath = dir "/" lg "/" lg "_user_languages.tsv";
      print >> fpath
  }'

# split tags by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT DISTINCT 
       COALESCE(s.lang, '\N'), 
       ts.sentence_id, 
       t.name 
     FROM `tags_sentences` ts
       JOIN `tags` t ON ts.tag_id = t.id
       JOIN `sentences` s ON ts.sentence_id = s.id" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      lg = ($1 == "\\N" ? "unknown" : $1);
      fpath = dir "/" lg "/" lg "_tags.tsv";
      print $2, $3 >> fpath
  }'

# split sentences in lists by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT 
       COALESCE(s.lang, '\N'), 
       sl.id, 
       s_sl.sentence_id
     FROM sentences_sentences_lists s_sl
       JOIN sentences_lists sl ON s_sl.sentences_list_id = sl.id
       JOIN sentences s ON s_sl.sentence_id = s.id
     WHERE sl.visibility != 'private'
     ORDER BY sl.id ASC, s_sl.sentence_id" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      lg = ($1 == "\\N" ? "unknown" : $1);
      fpath = dir "/" lg "/" lg "_sentences_in_lists.tsv";
      print $2, $3 >> fpath
  }'      

# split sentences with audio by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT
       COALESCE(s.lang, '\N'), 
       a.sentence_id, 
       COALESCE(u.username, '\N'), 
       COALESCE(u.audio_license, '\N'), 
       COALESCE(u.audio_attribution_url, '\N')
     FROM audios a 
       LEFT JOIN users u on u.id = a.user_id
       JOIN sentences s ON a.sentence_id = s.id
     ORDER BY sentence_id ASC" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      lg = ($1 == "\\N" ? "unknown" : $1);
      fpath = dir "/" lg "/" lg "_sentences_with_audio.tsv";
      print $2, $3, $4, $5 >> fpath
  }'    

find $TEMP_DIR -path '*tsv' -exec bzip2 -qf '{}' +
rm -rf $DL_DIR/per_language
rm transcriptions.csv
mv -f $TEMP_DIR $DL_DIR
