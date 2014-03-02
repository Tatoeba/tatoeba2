#!/bin/bash
# usage : ./add_lang  code  EnglishName  list_id
TATO_LANG=$1

#//@lang  
#/var/www-prod/app/models/sentence.php 
QUOTE_LANG="\"$1\""
sed -i  -e "s^//@lang^$QUOTE_LANG, //@lang^"  /var/www-prod/app/models/sentence.php

#'tgk' => __('Tajik', true),
#//@lang 
#/var/www-prod/app/views/helpers/languages.php
HELPER_LANG="$QUOTE_LANG => __('$2',true)"  
sed -i  -e "s^//@lang^$HELPER_LANG, //@lang^" /var/www-prod/app/views/helpers/languages.php

SEARCH_LANG="$QUOTE_LANG => '' "
sed -i  -e "s^//@lang^$SEARCH_LANG, //@lang^" /var/www-prod/docs/generate_sphinx_conf.php
#//@lang  
#/var/www-prod/app/models/sentence.php 
#
php /var/www-prod/docs/generate_sphinx_conf.php > /usr/local/etc/sphinx.conf

mysql -u… -p… tatoeba_prod -e "CALL add_new_language('$1', $3, 'blablablabla');"


