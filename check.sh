#!/bin/bash

set -e

check_failed() {
  echo "Check failed: $@"
  exit 1
}

if find webroot/img/flags/ -name "*.svg" -size +4k | grep .
then
  check_failed "the above file is too big"
fi

if grep -norz "<?[[:space:]]" src/
then
  check_failed "the above file contains a PHP short open tag"
fi

if ! php -l config/app_local.php.template
then
  check_failed "PHP syntax error in template file"
fi

for attr in width height; do
  if find webroot/img/flags/ -name "*.svg" -print0 \
    | xargs -0 -- grep --files-without-match "<svg[^<]* $attr=" \
    | grep .
  then
    check_failed "the above SVG file does not contain $attr attribute"
  fi
done

exit 0
