#!/bin/bash
#
# Translations updating tool for tatoeba (tatoeba.org).
# Provide an automated way to copy & compile po files from launchpad-bzr
# repository to mo files and commit then to the assembla-svn repository.
# 

function usage {
    echo -ne "$0 [-h] [-i INPUT_REPOSITORY] [-o OUTPUT_REPOSITORY]\n"
    echo -ne "Please indicate absolute paths.\n\n"
    echo -ne "-i INPUT_REPOSITORY\tWhere to put the launchpad repository,
        \t\tor if a local copy already exists.\n"
    echo -ne "-o OUTPUT_REPOSITORY\tWhere to put the assembla repository,
        \t\tor if a local copy already exists.
        \t\tPlease note that in this case, this path should
        \t\tindicate the 'app/locale' directory of the repository.\n"
}

TMP_DIR=/tmp/.fetch-translations-$(date +%F-%T)
LOG=$TMP_DIR/fetch.log

BZR=bzr

# If you want to make this an automated script, please fill this with
# --username and --password.
SVN=svn

LAUNCHPAD_LOCAL=$TMP_DIR/tatoeba-launchpad-bzr
ASSEMBLA_LOCAL=$TMP_DIR/tatoeba-assembla-svn

while getopts ":hi:o:" opt; do
    case $opt in
        h)
            usage >&2
            exit 1;;
        i)
            LAUNCHPAD_LOCAL=$OPTARG;;
        o)
            ASSEMBLA_LOCAL=$OPTARG;;
        \?)
            echo "$0: illegal option -- $OPTARG" >&2
            exit 1;;
        :)
            echo "$0: option requires an argument -- $OPTARG" >&2
            exit 1;;
    esac
done

LAUNCHPAD_REPO="https://code.launchpad.net/tatoeba"
ASSEMBLA_REPO="http://subversion.assembla.com/svn/tatoeba2/trunk/app/locale"

declare -A lng_tbl=(
          ["ar"]="ara" ["be"]="bel"
          ["de"]="deu" ["eo"]="epo"
          ["en"]="eng" ["eu"]="eus"
          ["es"]="spa" ["fr"]="fre"
          ["hi"]="hin" ["hu"]="hun"
          ["it"]="ita" ["ja"]="jpn"
          ["jbo"]="jbo" ["la"]="lat"
          ["nds"]="nds" ["pl"]="pol"
          ["pt_BR"]="pt_BR" ["ru"]="rus"
          ["tl"]="tgl" ["tr"]="tur" ["zh_CN"]="chi")

function clean_tmp {
    echo "Cleaning tmp directory: $TMP_DIR"
    rm -rf $TMP_DIR
}

function error_exit {
    error=$1

    echo "An error occured: $error"
    echo "Please verify $LOG"
    exit 1
}

# Preparing temp directory
mkdir -p $TMP_DIR

echo "Initiating translations fetch - $TMP_DIR" >> $LOG

# Fetching changes from launchpad-bzr repository
if [ -d $LAUNCHPAD_LOCAL ]; then
    echo "Pulling from launchpad repository"
    cd $LAUNCHPAD_LOCAL && $BZR pull $LAUNCHPAD_REPO &>> $LOG ||
        error_exit "bzr: error while pulling repository"
else
    echo "Cloning tatoeba launchpad repository"
    $BZR clone $LAUNCHPAD_REPO $LAUNCHPAD_LOCAL &>> $LOG ||
        error_exit "bzr: error while cloning repository"
fi

# Fetching changes from assembla-svn repository
if [ -d $ASSEMBLA_LOCAL ]; then
    echo "Pulling from assembla repository"
    cd $ASSEMBLA_LOCAL && $SVN update &>> $LOG
else
    echo "Checkout from assembla repository"
    $SVN checkout $ASSEMBLA_REPO $ASSEMBLA_LOCAL &>> $LOG
fi

LOCAL_DIR=$LAUNCHPAD_LOCAL/default

# Converting po files into mo and add them into the assembla-svn repository
for file in $(ls $LOCAL_DIR/*.po); do
    lng_in=$(basename $file .po)
    lng_to=${lng_tbl[$lng_in]}

    if [ -z $lng_to ]; then
        echo "$lng_in does not exists in the conversion table." &>> $LOG
    else
        dir=$ASSEMBLA_LOCAL/$lng_to
        dest=$dir/LC_MESSAGES/default.mo

        echo -ne "lng_in: '$lng_in' - lng_to: '$lng_to'\n" >> $LOG
        echo -ne "dir: $dir\ndest: $dest\n" >> $LOG

        # In case of a new language
        mkdir -p $dir/LC_MESSAGES

        echo "Converting ${lng_in}.po .."
        msgfmt -o $dest $file &>> $LOG
        cd $ASSEMBLA_LOCAL && $SVN add $dir &>> $LOG
    fi
done

cd $ASSEMBLA_LOCAL

if [ -z "$($SVN st)" ]; then
    echo "svn status: nothing has changed. will not commit."
else
    $SVN ci -m "Translations update." &>> $LOG ||
        error_exit "svn: error while commiting"
    echo "Changes commited."
fi

clean_tmp
echo "Done."
