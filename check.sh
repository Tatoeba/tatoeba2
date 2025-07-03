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

exit 0
