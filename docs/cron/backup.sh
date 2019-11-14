#!/bin/bash

BACKUP_LIST=(
    "/var/www-prod/app/webroot/img/profiles_128"
    "/var/www-prod/app/webroot/img/profiles_36"
    "/home/debian/tatowiki/tatowiki/app"
    "/var/www-audio/sentences"
    "/home/debian/dump/db.gz"
)

CONFIG_DIR="/home/debian/.gdrive/"
AUTH_JSON="tatoeba-backup.json"
GDRIVE_BIN="/home/debian/gdrive/gdrive"
GDRIVE(){
    $GDRIVE_BIN --config "$CONFIG_DIR" --service-account "$AUTH_JSON" "$@"
}

GDRIVE_FOLDER="0B3fWO5pH-xU1bFQ0aEFSQmNsMzA"
BACKUP_DIR="backupdir"
mkdir -p $BACKUP_DIR

# in Mbs
CHUNK_SIZE=32
CHUNK_SIZE=$(($CHUNK_SIZE*1024*1024))

KEEP_NUM=2

DATE=$(date -u +%F)

prune(){
    search_name=$1
    keep_num=$2

    flst=$(GDRIVE list --query "name contains '$search_name'" --order "createdTime desc" | tail -n +2);
    curr_num=$(echo "$flst" | wc -l)
    if [[ $curr_num -gt $keep_num ]]; then
        dlst=$(diff <(echo "$flst") <(echo "$flst" | head -n $keep_num) |\
         tail -n +2 | sed 's/< //g' | awk '{print $1}'
        )

        echo "Pruning $(echo "$dlst" | wc -l) files related to $search_name ..."
        IFS=$'\n' dlst=($dlst)
        for d in "${dlst[@]}"; do
            echo "Deleting $d"
            GDRIVE delete "$d"
        done
    fi
}

echo "Starting gdrive backup script at $DATE ..."

for i in "${BACKUP_LIST[@]}"; do
    # check for file or dir
    if [[ -f "$i" ]]; then
        f=$(echo "$i" | sed 's/^\///g; s/\//-/g')

        ext=$(echo "$f" | grep -Po "\..*")
        f=$(echo "$f" | sed s/\\..*$//g)

        search_name="$f"
        prune "$search_name" $(echo "$KEEP_NUM-1" | bc)

        f="$f""_""$DATE$ext"
        echo "Uploading file $f ..."
        GDRIVE upload --name "$f" --chunksize "$CHUNK_SIZE" "$i" -p "$GDRIVE_FOLDER"
    elif [[ -d "$i" ]]; then
        f=$(echo "$i" | sed 's/^\///g; s/\//-/g')

        search_name="$f"
        prune "$search_name" $(echo "$KEEP_NUM-1" | bc)

        f="$f""_""$DATE.tar"
        echo "Tarring dir $i into $f ..."
        tar -cf "$BACKUP_DIR/$f" "$i"
        echo "Uploading Tarred dir file $f"
        GDRIVE upload --delete --chunksize "$CHUNK_SIZE" "$BACKUP_DIR/$f" -p "$GDRIVE_FOLDER"
    fi
done
