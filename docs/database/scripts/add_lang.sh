#!/bin/bash
# See usage message below.
# After you execute the script, commit the updated files (sentence.php, etc.)
# to the repository.

# See http://en.wiki.tatoeba.org/articles/show/new-language-request# for more details.

USAGE=$'Usage:\n./add_lang code EnglishName list_id prod|dev MySQLUser MySQLPasswd MySQLDB\n\n'
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
if [ "$4" == "prod" ]; then
    PREFIX="/var/www-prod/"
elif [ "$4" == "dev" ]; then
    PREFIX="/home/tatoeba/tatoeba-www/"
else
    echo $USAGE
    exit 1
fi

LANGCODE=$1
LANGNAME=$2
LISTNUM=$3

#This line searches the file docs/generate_sphinx_conf.php for the
#comment string "//@lang". It inserts a string like the following before the comment:
#"nep" => ''
#and then executes the script.
QUOTE_LANG="\'$LANGCODE\'"
SEARCH_LANG="$QUOTE_LANG => \'$LANGNAME\' "
sed -i  -e "s^//@lang^\n    $SEARCH_LANG, //@lang^" $PREFIX"docs/generate_sphinx_conf.php"

# Do this early because there's a chance that this file 
# can't be written, in which case we want to exit immediately.
SPHINX_CONF="/usr/local/etc/sphinx.conf"
if [ ! -e $SPHINX_CONF ] || [ -w $SPHINX_CONF ]; then
    SPHINX_CMD="php $PREFIX""docs/generate_sphinx_conf.php"
    $SPHINX_CMD > "$SPHINX_CONF"
    if [ $? -ne 0 ]; then
        echo "Command failed:  $SPHINX_CMD > $SPHINX_CONF"
    fi
else
    echo "$SPHINX_CONF is not writable. Exiting."
    exit 1
fi

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

#Call the script docs/database/scripts/add_new_language.sql to update the database.
#We assume there is no language 'blablablabla'.
MUSER=$5
MPASSWORD=$6
MDB=$7
#Note the space between -u and the username, but not between -p and the password.
MYSQL_CMD="mysql -u $MUSER -p$MPASSWORD $MDB "'-e '
MYSQL_CMD=$MYSQL_CMD'"'
MYSQL_CMD=$MYSQL_CMD"CALL add_new_language('$LANGCODE', $LISTNUM, 'blablablabla');"
MYSQL_CMD=$MYSQL_CMD'"'
$MYSQL_CMD
if [ $? -ne 0 ]; then
    echo "Command failed:"
else
    echo "Command succeeded:"
fi
echo $MYSQL_CMD
