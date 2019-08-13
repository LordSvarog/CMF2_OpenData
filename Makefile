include .env

YII := framework/yii
PHP_BINARY := php
YII_BINARY := ${PHP_BINARY} ${YII}

.DEFAULT_GOAL := help
.PHONY: docker docker/up docker/down docker/ps docker/build supervisor supervisor/start supervisor/stop mysql/backup mysql/restore mysql/drop composer composer/install composer/update yii yii/up install update start stop

define do_exec
    @docker-compose exec -T --user="${APACHE_RUN_USER}:${APACHE_RUN_GROUP}" application ${1}
endef

docker: docker-compose.yml
	$(call do_exec, ${cmd})

docker/up: docker-compose.yml
	@docker-compose up --detach --remove-orphans

docker/down: docker-compose.yml
	@docker-compose down

docker/ps: docker-compose.yml
	@docker-compose ps

docker/build: docker-compose.yml
	@docker-compose build

supervisor: docker/containers/application/etc/supervisor/application.conf
	@docker-compose exec -T application supervisorctl --configuration=/etc/supervisor/application.conf ${cmd}

supervisor/start: docker/containers/application/etc/supervisor/application.conf
	@docker-compose exec -T application supervisorctl --configuration=/etc/supervisor/application.conf start all

supervisor/stop: docker/containers/application/etc/supervisor/application.conf
	@docker-compose exec -T application supervisorctl --configuration=/etc/supervisor/application.conf stop all

mysql/backup: docker/scripts/mysql-backup.sh
	$(shell docker/scripts/mysql-backup.sh ${path})
	@echo 'Done!'

mysql/restore: docker/scripts/mysql-restore.sh
	$(shell docker/scripts/mysql-restore.sh ${path})
	@echo 'Done!'

mysql/drop: docker/scripts/mysql-drop.sh
	$(shell docker/scripts/mysql-drop.sh)
	@echo 'Done!'

composer: html/framework/composer.json
	$(call do_exec, composer --working-dir=framework ${cmd})

composer/install: html/framework/composer.json
	$(call do_exec, composer install --working-dir=framework ${cmd})

composer/update: html/framework/composer.lock
	$(call do_exec, composer update --working-dir=framework ${cmd})

yii: html/framework/yii
	$(call do_exec, ${YII_BINARY} ${cmd})

yii/up: html/framework/yii
	$(call do_exec, ${YII_BINARY} migrate/up)
	$(call do_exec, ${YII_BINARY} access/install)
	$(call do_exec, ${YII_BINARY} cache/flush-all)

install: composer/install yii/up

update: composer/update yii/up

start: docker/up docker/ps

stop: docker/ps docker/down
