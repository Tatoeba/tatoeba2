#!/bin/bash
# See usage message below.
# After you execute the script, commit the updated files (sentence.php, etc.)
# to the repository.

# See http://en.wiki.tatoeba.org/articles/show/new-language-request# for more details.

# NOTE: THIS SCRIPT WAS BROKEN BY THE FOLLOWING COMMIT:

# https://github.com/Tatoeba/tatoeba2/commit/4d58dd5dca4645bfe07cf5ab9495150569e928dd#diff-3f96315be15bfac81691b06c5a0aabd4

# IT HAS NOT BEEN FIXED YET.

USAGE=$'Usage:\n./add_lang code EnglishName list_id local|dev|prod MySQLUser MySQLPasswd MySQLDB update|run|update_and_run\n\n'
USAGE=$USAGE$'Example: Add Nepali to your development machine. Update the files and then run them.\n'
USAGE=$USAGE$'First, search for \"Nepali\" on this page:\n'
USAGE=$USAGE$'    http://tatoeba.org/eng/sentences_lists/index\n\n' 
USAGE=$USAGE$'From the URL for the \"Nepali\" list, you can see that its id is 1297.\n'
USAGE=$USAGE$'Hence, the command is:\n'
USAGE=$USAGE$'    ./add_lang nep Nepali 1297 dev someuser somepwd somedb update_and_run\n'
USAGE=$USAGE$'Use \"update\" if you only want to update the files. This is a common scenario\n'
USAGE=$USAGE$'on a local VM, when you want to commit the files after updating them and do not need to run them.\n'
USAGE=$USAGE$'Use \"run\" if you only want to run the files. This is a common scenario\n'
USAGE=$USAGE$'on the server when the updated files have already been committed from elsewhere.\n'

UPDATE_FILES=0
RUN_FILES=0
if [ $# -ne 8 ]; then
    echo -e "$USAGE"
    exit 1
fi

if [ "$8" == "update" ]; then
    UPDATE_FILES=1
elif [ "$8" == "run" ]; then
    RUN_FILES=1
elif [ "$8" == "update_and_run" ]; then
    UPDATE_FILES=1
    RUN_FILES=1
else
    echo "Final argument must be \"update\", \"run\", or \"update_and_run\"."
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

ICONNAME=$PREFIX"app/webroot/img/flags/$1.svg"

if [ ! -e $ICONNAME ]; then
    echo "Please add icon for language and commit it as $ICONNAME ."
    echo "To create icon, follow instructions here:"
    echo "http://en.wiki.tatoeba.org/articles/show/adding-lang-to-corpus"
    exit 1
fi

# Do this early because there's a chance that this file 
# can't be written, in which case we want to exit immediately.
SPHINX_CONF="/etc/sphinxsearch/sphinx.conf"
if [ $UPDATE_FILES -eq 1 ]; then 
    if [ -e $SPHINX_CONF ] && [ ! -w $SPHINX_CONF ]; then
        echo "$SPHINX_CONF is not writable. You may need superuser privileges. Exiting."
        exit 1
    fi
fi

#Check whether the string is already present. We could do this for each of the
#files we're going to edit, but we do it just for the first.
grep "$SEARCH_LANG_RAW" $SPHINX_GEN
GREP_RESULT=$?
if [ $UPDATE_FILES -eq 1 ]; then 
    if [ $GREP_RESULT -eq 0 ]; then
        # We were told to update the files, but the specified lang is already present.
        echo -e "Terminating, since string already exists in $SPHINX_GEN:\n$SEARCH_LANG"
        exit 1;
    fi
    # Add a line to the file.
    sed -i  -e "s^//@lang^\n    $SEARCH_LANG, //@lang^" $SPHINX_GEN
else
    if [ $GREP_RESULT -ne 0 ]; then
        # We were told not to update the files, but the specified lang is not already present.
        echo -e "String not found in $SPHINX_GEN:\n$SEARCH_LANG_RAW"
        exit 1;
    fi
fi

if [ $RUN_FILES -eq 1 ]; then
    SPHINX_CMD="php $SPHINX_GEN"
    $SPHINX_CMD > "$SPHINX_CONF"
    if [ $? -ne 0 ]; then
        echo "Command failed:  $SPHINX_CMD > $SPHINX_CONF"
        exit 1;
    fi
fi

#This line searches the file app/vendors/languages_lib.php for the
#comment string "//@lang". It inserts a string like the following on a new line 
#before the comment:
#"nep" => __('Nepali', true),
LANGLIB_LANG="$QUOTE_LANG => __('$LANGNAME',true)"
if [ $UPDATE_FILES -eq 1 ]; then 
    sed -i  -e "s^//@lang^\n            $LANGLIB_LANG, //@lang^" $PREFIX"app/vendors/languages_lib.php"
fi

#Call the script docs/database/procedures/add_new_language.sql to update the database.
MUSER=$5
MPASSWORD=$6
MDB=$7

#Note the space between -u and the username, but not between -p and the password.
MYSQL_CMD="mysql -u $MUSER -p$MPASSWORD $MDB -e "

#First, store the procedure, in case it requires updating or has never been run on this machine.
if [ $RUN_FILES -eq 1 ]; then
    CMD1=$MYSQL_CMD'"'
    CMD1=$CMD1"\. ../procedures/add_new_language.sql"
    CMD1=$CMD1'"'
    eval $CMD1
    if [ $? -ne 0 ]; then
        echo "Command failed:"
        echo $CMD1
        if [ $UPDATE_FILES -eq 1 ]; then
            echo "Revert edited files if necessary."
        fi
    fi
fi

# Now run the procedure.
if [ $RUN_FILES -eq 1 ]; then
    CMD2=$MYSQL_CMD'"'
    CMD2=$CMD2"CALL add_new_language('$LANGCODE', $LISTNUM, null);"
    CMD2=$CMD2'"'
    eval $CMD2
    if [ $? -ne 0 ]; then
        echo "Command failed:"
        echo $CMD2
        if [ $UPDATE_FILES -eq 1 ]; then
            echo "Revert edited files if necessary."
        fi
        exit 1;
    fi
fi

echo "REMINDERS: "
echo "(1) If the language has a two-letter code (not all languages do), "
echo "    update the array in the function iso639_3_To_Iso639_1()"
echo "    in app/vendors/languages_lib.php."
echo "(2) If the new language is written right-to-left, "
echo "    update the array in the function getLanguageDirection()"
echo "    in app/vendors/languages_lib.php."
echo "(3) If the new language is stemmed, or is written with CJK characters, "
echo "    or contains characters not previously used in Tatoeba, "
echo "    or has no word boundaries, update generate_sphinx_conf.php."

