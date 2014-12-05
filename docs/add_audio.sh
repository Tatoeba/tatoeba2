#!/bin/bash 
# usage: ./add_audio.sh  DIRECTORY_CONTAINING_MP3s LANGUAGE_ISO_CODE MYSQL_USERNAME MYSQL_PASSWORD MYSQL_DB DEST_BASE
# See syntax message for an example.

shopt -s nullglob

if [ $# -ne 6 ]; then
    echo "Usage: add_audio.sh  DIR_W_MP3s LANG_CODE MYSQL_USER MYSQL_PWD MYSQL_DB DEST_BASE"
    echo "Example: add_audio.sh my_eng_sentences eng root tatoeba tatoeba /var/audio/sentences"
else
    MYSQL_USER=$3
    MYSQL_PASSWORD=$4
    MYSQL_DB=$5
    MP3DIR="$6/$2"
    if [ ! -d  "$MP3DIR" ]; then
        echo "Directory '$MP3DIR' must exist before running the script; execute 'mkdir $MP3DIR' if necessary."
        exit 1
    fi
    cd $1
    re='^[1-9][0-9]*$'
    rm -f update_hasaudio.sql
    for MP3 in *.MP3
        do
        SENTENCE_ID="${MP3%.MP3}"
        SRC_FILE="$SENTENCE_ID.MP3"
        DEST_FILE="$SENTENCE_ID.mp3"
        mv "$SRC_FILE" "$DEST_FILE"
        if [ "$?" -eq "0" ]; then
            echo "Moved '$SRC_FILE' to '$DEST_FILE'"
        else
            echo "Could not move '$SRC_FILE' to '$DEST_FILE'; script will terminate."
            exit 1
        fi
    done;
    for MP3 in *.mp3
        do
        SENTENCE_ID="${MP3%.mp3}";
        # We make sure that the sentence id is a number to avoid SQL injection and problems with
        # leading/trailing spaces in filenames.
        if [[ $SENTENCE_ID =~ $re ]] ; then
            echo "Adding $SENTENCE_ID.mp3";
            echo "UPDATE sentences SET hasaudio = 'shtooka' where id = $SENTENCE_ID;" >> update_hasaudio.sql;
        else
            echo "'$SENTENCE_ID.mp3' does not have the form 'dddd.mp3'; script will terminate."
            exit 1
        fi
        cp $SENTENCE_ID.mp3 "$MP3DIR"
        if [ "$?" -eq "0" ]; then
            echo "Copied $SENTENCE_ID.mp3 to $MP3DIR"
            chmod a+r $SENTENCE_ID.mp3
            if [ "$?" -eq "0" ]; then
                echo "Made $MP3DIR/$SENTENCE_ID.mp3 readable"
            else
                echo "Could not make $MP3DIR/$SENTENCE_ID.mp3 readable; script will terminate."
            fi
        else
            echo "Could not copy $SENTENCE_ID.mp3 to $MP3DIR; script will terminate."
            echo "You may need to run as root."
            exit 1
        fi
    done;

    mysql -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DB < update_hasaudio.sql;
    if [ "$?" -eq "0" ]; then
        echo "Updated database with the commands in $MP3DIR/update_hasaudio.sql"
    else
        echo "Could not update database"
        exit 1
    fi
fi
