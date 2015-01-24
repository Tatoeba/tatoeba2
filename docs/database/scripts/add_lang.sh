#!/bin/bash
# See usage message below.
# After you execute the script, commit the updated files (sentence.php, etc.)
# to the repository.

# See http://en.wiki.tatoeba.org/articles/show/new-language-request# for more details.

USAGE=$'Usage:\n./add_lang code EnglishName list_id local|dev|prod MySQLUser MySQLPasswd MySQLDB\n\n'
USAGE=$USAGE$'Example: Add Nepali to your development machine.\n'
USAGE=$USAGE$'First, search for \"Nepali\" on this page:\n'
USAGE=$USAGE$'    http://tatoeba.org/eng/sentences_lists/index\n\n' 
USAGE=$USAGE$'From the URL for the \"Nepali\" list, you can see that its id is 1297.\n'
USAGE=$USAGE$'Hence, the command is:\n'
USAGE=$USAGE$'    ./add_lang nep Nepali 1297 dev someuser somepwd somedb\n'

if (( $# != 7 )); then
    echo -e "$USAGE"
    exit 1
fi

PREFIX=""
if [ "$4" == "local" ]; then
    PREFIX="/home/tatoeba/tatoeba-www/"
elif [ "$4" == "dev" ]; then
    PREFIX="/var/www-dev/"
elif [ "$4" == "prod" ]; then
    PREFIX="/var/www-prod/"
else
    echo $USAGE
    exit 1
fi

LANGCODE=$1
LANGNAME=$2
LISTNUM=$3

ICONNAME=$PREFIX"app/webroot/img/flags/$1.png"

if [ ! -e $ICONNAME ]; then
    echo "Please add icon for language and commit it as $ICONNAME ."
    echo "To create icon, follow instructions here:"
    echo "http://en.wiki.tatoeba.org/articles/show/adding-lang-to-corpus"
    exit 1
fi

#This line searches the file docs/sphinx/generate_sphinx_conf.php for the
#comment string "//@lang". It inserts a string like the following before the comment:
#"nep" => ''
#and then executes the script.
QUOTE_LANG="\'$LANGCODE\'"
SEARCH_LANG="$QUOTE_LANG => \'$LANGNAME\' "
SPHINX_GEN=$PREFIX"docs/sphinx/generate_sphinx_conf.php"
SEARCH_LANG_RAW="'"$LANGCODE"'"" => ""'"$LANGNAME"'"

# Do this early because there's a chance that this file 
# can't be written, in which case we want to exit immediately.
SPHINX_CONF="/etc/sphinxsearch/sphinx.conf"
if [ ! -e $SPHINX_CONF ] || [ -w $SPHINX_CONF ]; then
    # The file does not exist, or can be overwritten. This is what we want.
    SPHINX_CMD="php $SPHINX_GEN"
    $SPHINX_CMD > "$SPHINX_CONF"
    if [ $? -ne 0 ]; then
        echo "Command failed:  $SPHINX_CMD > $SPHINX_CONF"
    fi
else
    echo "$SPHINX_CONF is not writable. You may need superuser privileges. Exiting."
    exit 1
fi

#Check whether the string is already present. We could do this for each of the
#files we're going to edit, but we do it just for the first.
grep "$SEARCH_LANG_RAW" $SPHINX_GEN
if [ $? -eq 0 ]; then
    echo "String already found: $SEARCH_LANG"
    exit 1;
fi
sed -i  -e "s^//@lang^\n    $SEARCH_LANG, //@lang^" $SPHINX_GEN

#This line searches the file app/models/sentence.php for the 
#comment string "//@lang". It inserts the three-letter language code (e.g., "nep"), 
#surrounded by quotes and followed by a quote, on a new line immediately before 
#the comment string.
sed -i  -e "s^//@lang^\n        $QUOTE_LANG, //@lang^"  $PREFIX"app/models/sentence.php"

#This line searches the file app/views/helpers/languages.php for the
#comment string "//@lang". It inserts a string like the following on a new line 
#before the comment:
#"nep" => __('Nepali', true),
HELPER_LANG="$QUOTE_LANG => __('$LANGNAME',true)"  
sed -i  -e "s^//@lang^\n            $HELPER_LANG, //@lang^" $PREFIX"app/views/helpers/languages.php"

#Call the script docs/database/procedures/add_new_language.sql to update the database.
#We assume there is no language 'NONEXISTENT_LANGUAGE'.
MUSER=$5
MPASSWORD=$6
MDB=$7
#Note the space between -u and the username, but not between -p and the password.
MYSQL_CMD="mysql -u $MUSER -p$MPASSWORD $MDB -e "
CMD1=$MYSQL_CMD'"'
CMD1=$CMD1"\. ../procedures/add_new_language.sql"
CMD1=$CMD1'"'
eval $CMD1
if [ $? -ne 0 ]; then
    echo "Command failed:"
    echo $CMD1
fi

CMD2=$MYSQL_CMD'"'
CMD2=$CMD2"CALL add_new_language('$LANGCODE', $LISTNUM, 'NONEXISTENT_LANGUAGE');"
CMD2=$CMD2'"'
eval $CMD2
if [ $? -ne 0 ]; then
    echo "Command failed:"
    echo $CMD2
elif
    echo "If the new language is written right-to-left, "
    echo "    update ../../../app/views/helpers/languages.php."
    echo "If the new language is stemmed, or is written with CJK characters, "
    echo "    or contains characters not previously used in Tatoeba, "
    echo "    or has no word boundaries, update generate_sphinx_conf.php."
fi


