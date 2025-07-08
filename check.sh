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

# Check that we won't run into XML id conflicts when merging all flags
# into webroot/cache_svg/allflags.svg
while IFS=':' read file id; do
  id="${id:4}"
  id="${id%\"}"
  if grep -l --exclude "$file" -o "id=\"$id\"" webroot/img/flags/*.svg
  then
    allids=( $(grep -o 'id="[^"]*"' webroot/img/flags/*.svg | cut -d '"' -f 2 | sort | uniq) )
    check_failed "id conflict: cannot use id=\"$id\" both in above file(s) and in $file" \
                 $'\n'"Used ids in all flags: ${allids[*]}"
  fi
done < <(grep -o 'id="[^"]*"' -r webroot/img/flags/*.svg | sort | uniq)

exit 0
