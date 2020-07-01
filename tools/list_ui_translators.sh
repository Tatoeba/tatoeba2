#!/bin/bash

set -e

TRANSIFEX_RC=~/.transifexrc

build_user_pass() {
  local username password

  username=$(grep ^username "$TRANSIFEX_RC" | sed 's, *= *,=,' | cut -f2 -d=)
  password=$(grep ^password "$TRANSIFEX_RC" | sed 's, *= *,=,' | cut -f2 -d=)

  if [ -n "$username" -a -n "$password" ]; then
    echo "$username:$password"
  fi
}

debug_enabled() {
  shopt -q -o xtrace
}

build_config() {
  printf 'user = "%s"\n' "$(build_user_pass)"
}

get_transifex() {
  local verbose="-s"

  if debug_enabled; then
    verbose="-v"
  fi

  build_config | curl $verbose --config - "$1"
}

parse_param() {
  local git_date param="$1"

  # Is it a git revision?
  if git_date=$(TZ=UTC git show "$param" --no-patch --pretty=tformat:%ad --date=local 2>/dev/null); then
    param="$git_date"
  fi

  date +"%Y-%m-%dT%H:%M:%S.%s" -d "$param" || die_usage
}

nice_date() {
  date -d "$1" +"%Y-%m-%d %H:%M"
}

suppress_cr() {
  sed 's,\r$,,'
}

get_translations_info() {
  local lang="$1" slug="$2"
  get_transifex "https://www.transifex.com/api/2/project/tatoeba_website/resource/$slug/translation/$lang/strings/?details"
}

extract_authors() {
  local from="$1" to="$2"
  jq -r '
map(select(.last_update > "'$from'" and .last_update < "'$to'")) |
group_by(.user) |
map_values({"key": (.[].user), "value": length}) |
sort_by(-.value) |
map_values("\(.key) (\(.value))") |
join(", ")' \
    | suppress_cr
}

get_locales() {
  get_transifex "https://www.transifex.com/api/2/project/tatoeba_website/resource/tatoebaResource/?details" | \
    jq -r '.available_languages | map((.code+" "+.name)) | .[]' | \
    suppress_cr
}

get_resources() {
  get_transifex 'https://www.transifex.com/api/2/project/tatoeba_website/resources' | \
    jq -r 'map((.name+" "+.slug)) | .[]' | \
    suppress_cr
}

display_stats() {
  local from_date="$1" to_date="$2"

  from_date_nice=$(nice_date "$from_date")
  to_date_nice=$(nice_date "$to_date")
  echo "Translations added between $from and $to ($from_date_nice - $to_date_nice):"

  readarray -t locales < <(get_locales)
  readarray -t resources < <(get_resources)

  for locale in "${locales[@]}"; do
    read code name <<<"$locale"
    have_contributors=
    for resource in "${resources[@]}"; do
      read pot slug <<<"$resource"
      contributors=$(get_translations_info "$code" "$slug" | extract_authors "$from_date" "$to_date")
      if [ -n "$contributors" ]; then
        printf " [%13s] %s (%s): %s\n" "$pot" "$name" "$code" "$contributors"
        have_contributors=1
      fi
    done

    if [ -z "$have_contributors" ]; then
      printf " [%13s] %s (%s): %s\n" "(all POTs)" "$name" "$code" "NOTHING"
    fi
  done
}

die_usage() {
  cat <<USAGE
Usage: $0 [<FROM> <TO>]

Gathers numbers about who last modified strings in what languages
in the time period between FROM and TO. FROM and TO can be a git
revision or a date(1) compatible date string.

If FROM and TO are omitted, it defaults to, respectively, the tag
next to last and the last tag.

Example: $0 "May 15" "June 15"
         $0 prod_2020-05-04 prod_2020-05-17
USAGE
  exit 1
}

check_transifex_rc() {
  if [ ! -f "$TRANSIFEX_RC" ]; then
    echo "Creating $TRANSIFEX_RC..."
    cat > "$TRANSIFEX_RC" <<EOF
[https://www.transifex.com]
api_hostname = https://api.transifex.com
hostname = https://www.transifex.com
username = 
password = 
EOF
  fi

  if [ -z "$(build_user_pass)" ]; then
    echo "Please add your transifex username and password in $TRANSIFEX_RC"
    exit 1
  fi
}

if [ "$#" -eq 0 ]; then
  from=$(git tag --sort=committerdate | tail -n 2 | head -n 1)
  to=$(git tag --sort=committerdate | tail -n 1)
elif [ "$#" -eq 2 ]; then
  from="$1"
  to="$2"
else
  die_usage
fi

check_transifex_rc

display_stats $(parse_param "$from") $(parse_param "$to")
