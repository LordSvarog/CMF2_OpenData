Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2-developer/yii2-auth "*"
```

or add

```
"yii2-developer/yii2-auth": "*"
```

to the require section of your `composer.json` file.

Use:
----

backend.php

```php
    'modules' => [
        'auth' => [
            'class' => \krok\auth\Module::class,
            'viewPath' => '@krok/auth/views/backend',
            'controllerNamespace' => 'krok\auth\controllers\backend',
        ],
    ],

    ...

    'components' => [
        'user' => [
            'class' => \yii\web\User::class,
            'identityClass' => \krok\auth\models\Auth::class,
            'idParam' => '__idBackend',
            'authTimeoutParam' => '__expireBackend',
            'absoluteAuthTimeoutParam' => '__absoluteExpireBackend',
            'returnUrlParam' => '__returnUrlBackend',
            'loginUrl' => ['/auth/default/login'],
            // http://www.yiiframework.com/doc-2.0/yii-web-user.html#$authTimeout-detail
            'authTimeout' => 1 * 60 * 60,
            'on afterLogin' => [
                \krok\auth\components\UserEventHandler::class,
                'handleAfterLogin',
            ],
            'on afterLogout' => [
                \krok\auth\components\UserEventHandler::class,
                'handleAfterLogout',
            ],
        ],
    ],
```

console.php

```php
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationTable' => '{{%migration}}',
            'useTablePrefix' => true,
            'interactive' => false,
            'migrationPath' => [
                '@krok/auth/migrations',
            ],
        ],
        'access' => [
            'class' => \krok\access\AccessController::class,
            'userIds' => [
                1,
            ],
            'rules' => [
                \krok\auth\rbac\AuthorRule::class,
            ],
            'config' => [
                [
                    'label' => 'Auth',
                    'name' => 'auth',
                    'controllers' => [
                        'default' => [
                            'label' => 'Default',
                            'actions' => [
                                'logout',
                            ],
                        ],
                        'auth' => [
                            'label' => 'Auth',
                            'actions' => [
                                'index',
                                'create',
                                'update',
                                'delete',
                                'view',
                                'refresh-token',
                            ],
                        ],
                        'log' => [
                            'label' => 'Log',
                            'actions' => ['index'],
                        ],
                        'profile' => [
                            'label' => 'Profile',
                            'actions' => ['index'],
                        ],
                    ],
                ],
            ],
        ],
    ],
```
