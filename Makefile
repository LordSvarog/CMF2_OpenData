include .env

YII := framework/yii
PHP_BINARY := php
YII_BINARY := ${PHP_BINARY} ${YII}

.DEFAULT_GOAL := help

define do_exec
    @docker-compose exec -T --user="${APACHE_RUN_USER}:${APACHE_RUN_GROUP}" application ${1}
endef

docker/exec: docker-compose.yml
	$(call do_exec, ${cmd})

docker/up: docker-compose.yml
	@docker-compose up --detach --remove-orphans

docker/down: docker-compose.yml
	@docker-compose down

docker/ps: docker-compose.yml
	@docker-compose ps

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

composer/install: html/framework/composer.json
	$(call do_exec, composer install --working-dir=framework)

composer/update: html/framework/composer.lock
	$(call do_exec, composer update --working-dir=framework)

yii: html/framework/yii
	$(call do_exec, ${YII_BINARY} ${cmd})

start: docker/up docker/ps

stop: docker/ps docker/down
