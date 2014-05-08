#!/bin/bash 
# Read in a directory of FLAC files and write out a directory of MP3 files.
# FLAC files are produced by Shtooka Recorder, while MP3 files are what we
# use to store audio on the server.
# usage: ./flac_to_mp3.sh  DIR_W_FLAC  DIR_TO_CONTAIN_MP3

if [ $# -ne 2 ]; then
    echo "Usage: flac_to_mp3.sh  DIR_W_FLAC DIR_TO_CONTAIN_MP3"
    echo "Example: flac_to_mp3.sh flac_dir mp3_dir"
else
    DIR_W_FLAC="$1"
    DIR_TO_CONTAIN_MP3="$2"
    if [ ! -d $DIR_W_FLAC ]; then
        echo "Input directory $DIR_W_FLAC does not exist."
    elif [ ! -d $DIR_TO_CONTAIN_MP3 ]; then
        echo "Output directory $DIR_TO_CONTAIN_MP3 does not exist."
    else
        cd $DIR_W_FLAC
        for FLAC in *.flac
        do
            BASENAME="${FLAC%.flac}";
            OUTPUT_FILE=${DIR_TO_CONTAIN_MP3}/${BASENAME}.mp3
            echo "Converting $DIR_W_FLAC/${FLAC} to $OUTPUT_FILE"
            flac -sdc ${FLAC} | lame - $OUTPUT_FILE
        done;
    fi;
fi;
