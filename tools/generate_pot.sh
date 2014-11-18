#!/bin/sh

POT=app/locale/default.pot
POT_DIR=$(dirname $POT)

list_source_files() {
    find app/ -iname '*.ctp' -o -iname '*.php' | LC_ALL=C sort
}

throw_to_gettext() {
    xargs xgettext \
        --language=php --from-code=UTF-8 \
        --output=- --color=no --no-wrap \
        --keyword=__ --keyword=__p:1c,2
}

adjust_file_refs() {
    sed 's/#: \([^ ]\+\) \([^ ]\+\)/#: \1\n#: \2/g' | \
    sed 's/^#: \([^:]\+\):\([0-9]\+\)/#: github.com\/Tatoeba\/tatoeba2\/tree\/master\/\1#L\2/'
}

xgettext --version >/dev/null 2>&1 || {
    echo "Error: xgettext not available."
    exit 1
}

[ -d "$POT_DIR" ] || {
    echo "Error: can't find directory $POT_DIR from here."
         "Please move to the root directory of the repository."
    exit 1
}

list_source_files | throw_to_gettext | adjust_file_refs > "$POT" \
    && echo "$POT created." \
    || echo "Something went wrong."
