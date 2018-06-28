#!/bin/sh

DB=$1
MIN_SENTENCE_ID=$2
MAX_SENTENCE_ID=$3

if [ $# -lt 3 ]; then
    echo "Usage: $0 <database-name> <min-sentence-id> <max-sentence-id> [mysqldump extra args]"
    echo "Note: use mysqldump otpion --skip-lock-tables to avoid freezing the website"
    exit 1
fi

shift 3
MYSQLDUMP_EXTRA_ARGS="$@"

run_mysqldump() {
    mysqldump $MYSQLDUMP_EXTRA_ARGS "$@"
}

run_mysqldump --routines "$DB" \
  acos \
  aros \
  aros_acos \
  contributions_stats \
  groups \
  languages \
  private_messages \
  sentences_lists \
  sinogram_subglyphs \
  sinograms \
  tags \
  users \
  users_languages \
  users_vocabulary \
  vocabulary \
  wall \
  wall_threads_last_message

build_condition() {
    local condition
    for field in "$@"; do
        [ -n "$condition" ] && condition="$condition and "
        condition="$condition(($field > $MIN_SENTENCE_ID and $field < $MAX_SENTENCE_ID) or $field = 0 or $field is null)"
    done
    echo "$condition"
}

run_mysqldump_partial_table() {
    local table condition
    table=$1
    shift
    condition=$(build_condition "$@")
    run_mysqldump "$DB" "$table" --where="$condition"
}

run_mysqldump_partial_table audios sentence_id
run_mysqldump_partial_table contributions sentence_id translation_id
run_mysqldump_partial_table last_contributions sentence_id translation_id
run_mysqldump_partial_table favorites_users favorite_id
run_mysqldump_partial_table reindex_flags sentence_id
run_mysqldump_partial_table sentence_annotations sentence_id
run_mysqldump_partial_table sentence_comments sentence_id
run_mysqldump_partial_table sentences id
run_mysqldump_partial_table sentences_sentences_lists sentence_id
run_mysqldump_partial_table sentences_translations sentence_id translation_id
run_mysqldump_partial_table tags_sentences sentence_id  
run_mysqldump_partial_table transcriptions sentence_id
run_mysqldump_partial_table users_sentences sentence_id

echo "CALL create_nbr_sentences_of_tag();"
echo "CALL create_nbr_sentences_of_list();"
if [ -f ./update_languages_stats.sql ]; then
    cat ./update_languages_stats.sql
else
    echo "SELECT 'Please run the update_languages_stats.sql script to finalize import' as '';"
fi
