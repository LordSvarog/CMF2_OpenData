#!/usr/bin/env sh

. ./.env

do_mysql_restore() {

    DIRECTORY="docker/backup"
    if ! [ -d "$DIRECTORY" ]
    then
        mkdir --mode=0700 --parents "$DIRECTORY"
    fi

    if [ -z "$1" ]
    then
        FILE=$(find "$DIRECTORY" -type f -name *.sql.gz -exec ls -t "{}" + | head -1)
        COMMAND="$FILE"
    else
        COMMAND="$1"
    fi

    gunzip < "$COMMAND" | docker-compose exec -T --env="MYSQL_PWD=$MYSQL_PASSWORD" mysql mysql --user="$MYSQL_USER" "$MYSQL_DATABASE"
}

do_mysql_restore $@
