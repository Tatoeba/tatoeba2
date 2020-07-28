#!/bin/bash

POT=src/Locale/default.pot
POT_DIR=$(dirname $POT)
POT_TMP=$(mktemp --suffix=.po)

# set the sighandlers asap
trap "rm -f $POT_TMP; exit" SIGHUP SIGINT SIGTERM

list_source_files() {
    git ls-files src/ \
        | grep '.\(ctp\|php\)$' \
        | LC_ALL=C sort
}

throw_to_gettext() {
    xargs xgettext \
        --language=php --from-code=UTF-8 \
        --output=- --color=no --no-wrap \
        --add-comment=@translators \
        "$@"
}

cosmetics() {
    tr -d '\r' | \
    sed 's/#: \([^ ]\+\) \([^ ]\+\)/#: \1\n#: \2/g' | \
    sed 's/^#: \([^:]\+\):\([0-9]\+\)/#: github.com\/Tatoeba\/tatoeba2\/tree\/dev\/\1#L\2/' | \
    sed '/#\. @translators:/ {s/@translators: *//; :a; /^#[^.]/!{s,@translators: *,/ ,; n; ba;};}'
}

get_all_contexts_from() {
    local pofile="$1"
    grep '^msgctxt' "$pofile" | sort | uniq | cut -d'"' -f2
}

write_domain_pot_from_context() {
    local domain="$1"
    local domain_potfile="$POT_DIR"/"$1".pot
    msggrep \
        --output=- --color=no \
        --msgctxt --fixed-strings --regexp="$domain" "$POT_TMP" | \
        grep -v '^msgctxt' > "$domain_potfile" \
        && echo "$domain_potfile created." \
        || echo "Something went wrong while creating $domain_potfile."
}

xgettext --version >/dev/null 2>&1 || {
    echo "Error: xgettext not available."
    exit 1
}

[ -d "$POT_DIR" ] || {
    echo "Error: can't find directory $POT_DIR from here."
    echo "Please move to the root directory of the repository."
    exit 1
}

# Extract strings to default.pot
DEFAULT_DOMAIN_K='--keyword=__ --keyword=__x:1c,2 --keyword=__n:1,2 --keyword=__xn:1c,2,3'
list_source_files | \
    throw_to_gettext $DEFAULT_DOMAIN_K | \
    cosmetics > "$POT" \
    && echo "$POT created." \
    || echo "Something went wrong while creating $POT."

# Extract strings to domain-specific pot files.
#
# XGettext doesn't allow to easily extract strings to different files.
# To work around this problem, we first analyse domain-specific strings
# using the domain as msgctxt. Then, we split each contextualized string
# into a different file
OTHER_DOMAINS_K='--keyword=__d:1c,2'
list_source_files | \
    throw_to_gettext $OTHER_DOMAINS_K | \
    cosmetics > "$POT_TMP"

# sounds like the charset is autodetected and the autodetection fails
sed -i 's/charset=CHARSET/charset=utf-8/' "$POT_TMP"

for domain in $(get_all_contexts_from "$POT_TMP"); do
    write_domain_pot_from_context "$domain"
done

rm -f $POT_TMP
