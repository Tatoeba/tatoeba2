#!/bin/bash
#
# Translations updating tool for tatoeba (tatoeba.org).
# Provide an automated way to copy & compile po files from launchpad-bzr
# repository to mo files and commit then to the main repository on github.


function usage {
    echo -ne "$0 [-h] [-c] [-n] [-i INPUT_REPOSITORY] [-o OUTPUT_REPOSITORY]\n"
    echo -ne "Please indicate absolute paths.\n\n"
    echo -ne "-c Commit changes.\n"
    echo -ne "-n Do not clean the temp directory.\n"
    echo -ne "-i INPUT_REPOSITORY\tWhere to put the launchpad repository,
        \t\tor if a local copy already exists.\n"
    echo -ne "-o OUTPUT_REPOSITORY\tWhere to put the main repository,
        \t\tor if a local copy already exists.
        \t\tPlease note that in this case, this path should
        \t\tindicate the 'app/locale' directory of the repository.\n"
}

TMP_DIR=/tmp/.fetch-translations-$(date +%F-%T)
LOG=$TMP_DIR/fetch.log

BZR=bzr

GIT=git

TRANSLATIONS_LOCAL=$TMP_DIR/tatoeba-launchpad-bzr
MAIN_LOCAL=$TMP_DIR/tatoeba-github-git

while getopts ":hcni:o:" opt; do
    case $opt in
        h)
            usage >&2
            exit 1;;
        c)
            COMMIT=yes;;
        n)
            DONTCLEAN=yes;;
        i)
            TRANSLATIONS_LOCAL=$OPTARG;;
        o)
            MAIN_LOCAL=$OPTARG;;
        \?)
            echo "$0: illegal option -- $OPTARG" >&2
            exit 1;;
        :)
            echo "$0: option requires an argument -- $OPTARG" >&2
            exit 1;;
    esac
done

test -z "$COMMIT" && echo "Will not commit."

TRANSLATIONS_ORIGIN="https://code.launchpad.net/tatoeba"
MAIN_ORIGIN="https://github.com/Tatoeba/tatoeba2.git"

declare -A lng_tbl=(
          ["ar"]="ara"
          ["az"]="aze"
          ["be"]="bel"
          ["ca"]="cat"
          ["cs"]="ces"
          ["da"]="dan"
          ["de"]="deu"
          ["el"]="ell"
          ["en"]="eng"
          ["en_GB"]="en_GB"
          ["eo"]="epo"
          ["es"]="spa"
          ["et"]="est"
          ["eu"]="eus"
          ["fi"]="fin"
          ["fr"]="fre"
          ["gl"]="glg"
          ["hi"]="hin"
          ["hu"]="hun"
          ["ia"]="ina"
          ["it"]="ita"
          ["ja"]="jpn"
          ["jbo"]="jbo"
          ["ka"]="kat"
          ["ko"]="kor"
          ["la"]="lat"
          ["lt"]="lit"
          ["mr"]="mar"
          ["ms"]="msa"
          ["nds"]="nds"
          ["nl"]="nld"
          ["oc"]="oci"
          ["pl"]="pol"
          ["pt_BR"]="pt_BR"
          ["ru"]="rus"
          ["ro"]="ron"
          ["sv"]="swe"
          ["tl"]="tgl"
          ["tr"]="tur"
          ["uk"]="ukr"
          ["uz"]="uzb"
          ["vi"]="vie"
          ["xal"]="xal"
          ["zh_CN"]="chi")

function clean_tmp {
    echo "Cleaning tmp directory: $TMP_DIR"
    rm -rf $TMP_DIR
}

function error_exit {
    error=$1

    echo "An error occurred: $error"
    echo "See $LOG"
    exit 1
}

# Preparing temp directory
mkdir -p $TMP_DIR

echo "Initiating translations fetch - $TMP_DIR" >> $LOG

# Fetching changes from launchpad-bzr repository
if [ -d $TRANSLATIONS_LOCAL ]; then
    echo "Pulling from launchpad repository"
    cd $TRANSLATIONS_LOCAL && $BZR pull $TRANSLATIONS_ORIGIN &>> $LOG ||
        error_exit "bzr: error while pulling repository"
else
    echo "Cloning tatoeba launchpad repository"
    $BZR branch $TRANSLATIONS_ORIGIN $TRANSLATIONS_LOCAL &>> $LOG ||
        error_exit "bzr: error while cloning repository"
fi

# Fetching changes from the main git repository
if [ -d $MAIN_LOCAL ]; then
    echo "Pulling from the main git repository"
    cd $MAIN_LOCAL && $GIT pull &>> $LOG ||
      error_exit "git: error while pulling repository"
else
    echo "Checkout from the main git repository"
    $GIT clone $MAIN_ORIGIN $MAIN_LOCAL &>> $LOG ||
      error_exit "git: error while cloning repository"
fi

LOCAL_DIR=$TRANSLATIONS_LOCAL/default

# Converting po files into mo and add them into the git repository
for file in $(ls $LOCAL_DIR/*.po); do
    lng_in=$(basename $file .po)
    lng_to=${lng_tbl[$lng_in]}

    if [ -z $lng_to ]; then
        echo "$lng_in does not exists in the conversion table." &>> $LOG
    else
        dir=$MAIN_LOCAL/app/locale/$lng_to
        dest=$dir/LC_MESSAGES/default.mo

        echo -ne "lng_in: '$lng_in' - lng_to: '$lng_to'\n" >> $LOG
        echo -ne "dir: $dir\ndest: $dest\n" >> $LOG

        # In case of a new language
        mkdir -p $dir/LC_MESSAGES

        echo "Converting ${lng_in}.po .."
        msgfmt -o $dest $file &>> $LOG
        cd $MAIN_LOCAL && $GIT add $dir &>> $LOG
    fi
done

cd $MAIN_LOCAL

if [ -z "$($GIT status)" ]; then
    echo "git status: nothing has changed. will not commit."
else
    if [ -n "$COMMIT" ]; then
        $GIT commit -m "Translations updated via update-translations.sh." &>> $LOG ||
          error_exit "git: error while committing"
        echo "Changes have been committed."
        $GIT push origin master &>> $LOG ||
          error_exit "git: error while pushing"
        echo "Changes have been pushed to master."
    else
        echo "Changes haven't been committed. Use -c to commit."
    fi
fi

test -z "$DONTCLEAN" && clean_tmp

echo "Done."
