Code style:
===========
* https://github.com/yiisoft/yii2/blob/master/docs/internals-ru/core-code-style.md

Pattern:
========
* https://ru.wikipedia.org/wiki/SOLID_(объектно-ориентированное_программирование)

MySQL
=====
* explicit_defaults_for_timestamp: https://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_explicit_defaults_for_timestamp

Common
======
* behaviors, grid, traits, interfaces, validators, widgets - ( "yii2-developer/yii2-extend": "*", ) лежат в krok\extend
* Имена полей в таблицах базы данных camelCase
* Имена директорий в camelCase
* Имена переменных в camelCase
* Реляции получают суффикс Relation ( getCreatedByRelation )
* Наименования модулей как и имена таблиц в ***единственном*** числе!
* Не использовать before* и after* методы \yii\base\Model

Editor
======
```php
<?= $form->field($model, 'text')->widget(\krok\editor\EditorWidget::class) ?>
```

Model
=====
* id - должно быть!
* hidden - для записи, active - для учетных записей. Вариант выбора в виде select поля.
    * html/framework/vendor/yii2-developer/yii2-extend/src/interfaces/HiddenAttributeInterface.php и html/framework/vendor/yii2-developer/yii2-extend/src/traits/HiddenAttributeTrait.php
    * html/framework/vendor/yii2-developer/yii2-extend/src/interfaces/BlockedAttributeInterface.php и html/framework/vendor/yii2-developer/yii2-extend/src/traits/BlockedAttributeTrait.php
* createdBy - создатель записи, внешний ключ на таблицу пользователей, ( DELETE - SET NULL , UPDATE - RESTRICT ) поведение - krok\extend\behaviors\CreatedByBehavior::class
* createdBy не нужно выводить в шаблонах и фильтре
* createdAt - дата и время создания, updatedAt - дата и время последнего обновления. Поведение - krok\extend\behaviors\TimestampBehavior::class

Форматтер типов, для:
--------------------

**view.php**

```php
'createdAt:datetime',
'updatedAt:datetime',
```

**index.php**

```php
[
    'class' => krok\extend\grid\ActiveColumn::class,
    'attribute' => 'title',
],
[
    'class' => krok\extend\grid\HiddenColumn::class,
],
[
    'class' => krok\extend\grid\DatePickerColumn::class,
    'attribute' => 'createdAt',
],
[
    'class' => krok\extend\grid\DatePickerColumn::class,
    'attribute' => 'updatedAt',
],
```

Для примера можно взять модуль - @vendor/yii2-developer/yii2-content
--------------------------------------------------------------------

Установка
=========

Предполагается что в системе уже установлен docker и docker-compose

Копируем файл настройки окружения:

```
cp .env.dist .env
```

Редактируем файл .env

Основные параметры:

```
COMPOSE_PROJECT_NAME=cmf2 _ИМЯ_ПРОЕКТА_

CONTAINER_NAME=cmf2 _ИМЯ_ПРОЕКТА_

APACHE_RUN_ID=1000 _ID_ЛОКАЛЬНОГО_ПОЛЬЗОВАТЕЛЯ_
APACHE_RUN_USER=krok _ИМЯ_ЛОКАЛЬНОГО_ПОЛЬЗОВАТЕЛЯ_
APACHE_RUN_GROUP=krok _ГРУППА_ЛОКАЛЬНОГО_ПОЛЬЗОВАТЕЛЯ_
```

Остальные параметры можно оставить без изменения

Создаем файл настройки приложения:

```
nano html/.env.local
```

В нем можно переопределять или устанавливать настройки из файла:

```
html/.env
```

Запускаем контейнеры:

```
docker-compose up -d
```

Если контейнеров в системе нет они будут загружены ( ~2 GB ) .

После первого запуска нужно подождать 2-3 минуты , это нужно для первой инициализации

Проверяем запущенные контейнеры:

```
docker-compose ps
```

Установка системы

```
./docker.sh install
```

Администрирование
=================

Адрес: domain.tld/cp

Логин: webmaster

Пароль: webmaster
