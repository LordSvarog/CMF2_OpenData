#!/usr/bin/env sh

. ./.env

do_mysql_backup() {

    DIRECTORY="docker/backup"
    if ! [ -d "$DIRECTORY" ]
    then
        mkdir --mode=0700 --parents "$DIRECTORY"
    fi

    if [ -z "$1" ]
    then
        DATE=$(date +"%Y-%m-%d-%H:%M")
        FOLDER="$DIRECTORY/$DATE"

        if ! [ -d "$FOLDER" ]
        then
            mkdir --mode=0700 --parents "$FOLDER"
        fi

        COMMAND="$FOLDER/latest.sql.gz"
    else
        COMMAND="$1"
    fi

    docker-compose exec -T --env="MYSQL_PWD=$MYSQL_PASSWORD" mysql mysqldump --single-transaction --no-create-db --verbose --user="$MYSQL_USER" "$MYSQL_DATABASE" | gzip > "$COMMAND"

    find "$DIRECTORY" -type d -mtime +3 -exec rm -r {} +
}

do_mysql_backup $@
