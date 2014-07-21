#!/bin/bash
# Usage: ./benchmark.sh 10 1000 http://127.0.0.1:8080 bench1
# apache benchmark 'ab' should be installed on your system
c=$1
n=$2
u=$3
i=$4

URLS=(
    '/'
    '/home'
    '/sentences/show/random'
    '/sentences/show_all_in/eng/none/none'
    '/sentences_lists/index'
    '/tags/view_all'
    '/sentences/with_audio'
    '/sentences/show/2395875'
    '/contributions/latest'
    '/contributions/activity_timeline'
    '/stats/sentences_by_language'
    '/sentences/show_all_in/eng/none/none/indifferent'
    '/wall/index'
    '/sentence_comments/index'
    '/sentences/add'
    '/activities/translate_sentences'
    '/activities/adopt_sentences/eng'
    '/activities/improve_sentences'
    '/users/all'
)

echo -e "starttime\tseconds\tctime\tdtime\tttime\twait\tconcurrency\ttotal requests\turl" > "$i"".csv"

for url in ${URLS[@]}; do
    ab -c $c -n $n -g tmp "$u$url"
    awk "{ if (NR>1) { "'print $0'"\"\t$c\t$n\t$url\"} }" tmp >> "$i"".csv" 
done

rm -f tmp
