#!/bin/bash

set -e

check_prerequistes() {
  if ! which tx >/dev/null 2>&1; then
    echo "Please install the transifex client first:"
    echo "  https://docs.transifex.com/client/installing-the-client"
    exit 1
  fi

  rcfile="$HOME/.transifexrc"
  if [ ! -f "$rcfile" ]; then
    echo "Creating $rcfile..."
    cat > "$rcfile" <<EOF
[https://www.transifex.com]
api_hostname = https://api.transifex.com
hostname = https://www.transifex.com
username = 
password = 
EOF
  fi
  if grep -q "^username[[:space:]]\+=[[:space:]]*$" $rcfile; then
    echo "Please add your transifex username and password in $rcfile"
    exit 1
  fi
}

pull_translations() {
  echo "Pulling translations from Transifex..."

  tx_params="-f -a"
  tx_version=$(tx --version | cut -f1 -d, | tr -d .)
  if [ $tx_version -ge 0133 ]; then
    tx_params="--no-interactive $tx_params"
  fi
  if [ $tx_version -ge 0132 ]; then
    tx_params="--parallel $tx_params"
  fi

  if [ "$1" -eq 1 ]; then
    tx pull $tx_params
  else
    tx pull $tx_params -r tatoeba_website.tatoebaResource,tatoeba_website.countries,tatoeba_website.tatoeba-languages,tatoeba_website.admin
    tx pull $tx_params --minimum-perc 100 -r tatoeba-terms-of-use.terms-of-use
  fi
}

remove_cakephp_cached_translation() {
  echo "Cleaning CakePHP cache..."
  find ./tmp/cache/persistent/ -type f \! -name empty \! -name 'myapp_cake_core_translations_cake_*' -exec rm -f {} \;
}

pull_all=0
if [ "$1" = "-a" ]; then
  pull_all=1
elif [ "$#" -gt 0 ]; then
  echo "Usage: $0 [-a]"
  echo " -a: force pulling all translations, even incomplete terms of use"
  exit 1
fi

check_prerequistes
pull_translations $pull_all
remove_cakephp_cached_translation

exit 0
