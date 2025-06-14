#!/bin/bash

set -e

# be nice, give priority to other processes
renice -n 19 -p $$ >/dev/null
ionice -c idle -p $$

echo "Starting export at $(date -Iseconds)"
ROOT='/var/www-prod'
# To test...
# ROOT='/home/vagrant/Tatoeba'

rm -f /var/tmp/*.csv
mkdir -p "$DL_DIR"

echo "Starting SQL scripts at $(date -Iseconds)"
mysql -u "$DB_USER" -p"$DB_PASS" "$DB" < $ROOT/docs/database/scripts/weekly_exports.sql
mv /var/tmp/*csv "$DL_DIR"

mysql -u "$DB_USER" -p"$DB_PASS" "$DB" < "$ROOT"/docs/database/scripts/wwwjdic.sql
mv /var/tmp/*csv "$DL_DIR"

echo "Starting tarring at $(date -Iseconds)"
cd "$DL_DIR"
tar -cjf sentences_base.tar.bz2 sentences_base.csv
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
tar -cjf sentences_base.tar.bz2 sentences_base.csv

echo "Starting language splitting for sentences at $(date -Iseconds)"
# Create per-language files for the different sentences files
TEMP_DIR='/var/tmp/per_language'
trap "rm -rf $TEMP_DIR" EXIT

echo 'SELECT code FROM languages WHERE code IS NOT NULL;' |
( mysql --skip-column-names tatoeba; echo 'unknown' ) |
xargs -I @ mkdir -p $TEMP_DIR/@

split_file () {
    local BASENAME=_${1%csv}tsv
    local DIR=${TEMP_DIR}/

    awk -F"\t" -v basename=$BASENAME -v dir=$DIR \
    '{print >> ($2 == "\\N" || $2 == "" ? dir "unknown/unknown" basename : dir $2 "/" $2 basename)}' \
    $1
}

split_file sentences_detailed.csv
split_file sentences.csv
split_file sentences_CC0.csv
split_file transcriptions.csv

echo "Starting language splitting for links at $(date -Iseconds)"
# split links by language pair
mysql --skip-column-names --batch tatoeba -e \
    "SELECT 
      sentence_lang, 
      translation_lang,
      sentence_id, 
      translation_id
     FROM sentences_translations" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      sentence_lang = ($1 == "NULL" || $1 == "" ? "unknown" : $1);
      translation_lang = ($2 == "NULL" || $2 == "" ? "unknown" : $2);
      fpath = dir "/" sentence_lang "/" sentence_lang "-" translation_lang "_links.tsv";
      print $3, $4 >> fpath;
      close(fpath)
  }'

echo "Starting language splitting for user languages at $(date -Iseconds)"
# split user languages by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT
       ul.language_code, 
       ul.level, 
       u.username, 
       ul.details
     FROM users_languages ul 
       LEFT JOIN users u ON ul.of_user_id = u.id
     ORDER BY ul.language_code ASC, ul.level DESC, u.username ASC" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      language_code = ($1 == "NULL" || $1 == "" ? "unknown" : $1);      
      level = ($2 == "NULL" ? "\\N" : $2);      
      username = ($3 == "NULL" ? "\\N" : $3);      
      fpath = dir "/" language_code "/" language_code "_user_languages.tsv";
      print $1, level, username, $4 >> fpath
  }'

echo "Starting language splitting for tags at $(date -Iseconds)"
# split tags by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT DISTINCT 
       s.lang, 
       ts.sentence_id, 
       t.name 
     FROM tags_sentences ts
       JOIN tags t ON ts.tag_id = t.id
       JOIN sentences s ON ts.sentence_id = s.id" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      lang = ($1 == "NULL" || $1 == "" ? "unknown" : $1);
      fpath = dir "/" lang "/" lang "_tags.tsv";
      print $2, $3 >> fpath
  }'

echo "Starting language splitting for sentence lists at $(date -Iseconds)"
# split sentences in lists by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT 
       s.lang, 
       sl.id, 
       s_sl.sentence_id
     FROM sentences_sentences_lists s_sl
       JOIN sentences_lists sl ON s_sl.sentences_list_id = sl.id
       JOIN sentences s ON s_sl.sentence_id = s.id
     WHERE sl.visibility != 'private'
     ORDER BY sl.id ASC, s_sl.sentence_id" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      lang = ($1 == "NULL" || $1 == "" ? "unknown" : $1);
      fpath = dir "/" lang "/" lang "_sentences_in_lists.tsv";
      print $2, $3 >> fpath
  }'      

echo "Starting language splitting for audio at $(date -Iseconds)"
# split sentences with audio by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT
       s.lang, 
       a.sentence_id, 
       a.id,
       u.username, 
       u.audio_license, 
       u.audio_attribution_url
     FROM audios a 
       LEFT JOIN users u on u.id = a.user_id
       JOIN sentences s ON a.sentence_id = s.id
     ORDER BY sentence_id ASC" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      lang = ($1 == "NULL" || $1 == "" ? "unknown" : $1);
      username = ($4 == "NULL" ? "\\N" : $4);
      audio_license = ($5 == "NULL" ? "\\N" : $5);
      audio_attribution_url = ($6 == "NULL" ? "\\N" : $6);
      fpath = dir "/" lang "/" lang "_sentences_with_audio.tsv";
      print $2, $3, username, audio_license, audio_attribution_url >> fpath
  }'  

echo "Starting language splitting for sentence bases at $(date -Iseconds)"
# split sentences base by language
mysql --skip-column-names --batch tatoeba -e \
    "SELECT
       s.lang,     
       s.id,
       s.based_on_id
     FROM sentences s
     WHERE correctness > -1 AND license != ''" | \
  awk -F"\t" -v dir=$TEMP_DIR 'BEGIN {OFS = "\t"} {
      lang = ($1 == "NULL" || $1 == "" ? "unknown" : $1);
      based_on_id = ($3 == "NULL" ? "\\N" : $3);
      fpath = dir "/" lang "/" lang "_sentences_base.tsv";
      print $2, based_on_id >> fpath
  }'    

echo "Starting cleanup at $(date -Iseconds)"
find $TEMP_DIR -path '*tsv' -exec bzip2 -qf '{}' +
rm -rf $DL_DIR/per_language
rm transcriptions.csv
mv -f $TEMP_DIR $DL_DIR
echo "Finished at $(date -Iseconds)"
