#!/usr/bin/env sh

. ./.env

do_mysql_drop() {

    if ! [ -z "$1" ]
    then
        TABLE="$1"
        docker-compose exec -T --env="MYSQL_PWD=$MYSQL_PASSWORD" mysql mysql --user="$MYSQL_USER" "$MYSQL_DATABASE" -e "SET FOREIGN_KEY_CHECKS=0; DROP TABLE $TABLE;"
    fi
}

do_mysql_truncate() {

    TABLES=$(docker-compose exec -T --env="MYSQL_PWD=$MYSQL_PASSWORD" mysql mysql --user="$MYSQL_USER" "$MYSQL_DATABASE" -Nse "SHOW TABLES;")

    for TABLE in $TABLES
    do
        do_mysql_drop "$TABLE"
    done
}

do_mysql_truncate
