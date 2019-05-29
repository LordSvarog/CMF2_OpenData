Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2-developer/yii2-cabinet "*"
```

or add

```
"yii2-developer/yii2-cabinet": "*"
```

to the require section of your `composer.json` file.

Configure
---------

backend

```
    'modules' => [
        'cabinet' => [
            'class' => \krok\cabinet\Module::class,
            'viewPath' => '@krok/cabinet/views/backend',
            'controllerNamespace' => 'krok\cabinet\controllers\backend',
        ],
    ],
```

frontend

```
    'modules' => [
        'cabinet' => [
            'class' => \krok\cabinet\Module::class,
            'viewPath' => '@krok/cabinet/views/frontend',
            'controllerNamespace' => 'krok\cabinet\controllers\frontend',
            'as beforeRequest' => [
                'class' => \yii\filters\AccessControl::class,
                'except' => [
                    'auth/index',
                    'login/oauth',
                    'login/login',
                    'login/captcha',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ],
    ],
    'components' => [
        'user' => [
            'class' => \krok\cabinet\components\User::class,
            'identityClass' => \krok\cabinet\models\Client::class,
            'loginUrl' => ['/cabinet/login/login'],
            // http://www.yiiframework.com/doc-2.0/yii-web-user.html#loginRequired()-detail
            'returnUrl' => ['/cabinet/default/index'],
            // Whether to enable cookie-based login: Yii::$app->user->login($this->getUser(), 24 * 60 * 60)
            'enableAutoLogin' => false,
            // http://www.yiiframework.com/doc-2.0/yii-web-user.html#$authTimeout-detail
            'authTimeout' => 1 * 60 * 60,
            'on afterLogin' => [
                \krok\cabinet\components\ClientEventHandler::class,
                'handleAfterLogin',
            ],
            'on afterLogout' => [
                \krok\cabinet\components\ClientEventHandler::class,
                'handleAfterLogout',
            ],
        ],
        'authClientCollection' => [
            'class' => \yii\authclient\Collection::class,
            'clients' => [
                'yandex' => [
                    'class' => \krok\oauth\Yandex::class,
                    'clientId' => '',
                    'clientSecret' => '',
                    'normalizeUserAttributeMap' => [
                        'email' => 'default_email',
                    ],
                ],
                'google' => [
                    'class' => \krok\oauth\Google::class,
                    'clientId' => '',
                    'clientSecret' => '',
                    'normalizeUserAttributeMap' => [
                        'login' => ['emails', 0, 'value'],
                        'email' => ['emails', 0, 'value'],
                    ],
                ],
                'vkontakte' => [
                    'class' => \krok\oauth\VKontakte::class,
                    'clientId' => '',
                    'clientSecret' => '',
                    'normalizeUserAttributeMap' => [
                        'id' => 'user_id',
                        'login' => 'screen_name',
                    ],
                ],
                'facebook' => [
                    'class' => \krok\oauth\Facebook::class,
                    'clientId' => '',
                    'clientSecret' => '',
                    'normalizeUserAttributeMap' => [
                        'login' => 'id',
                    ],
                ],
                'twitter' => [
                    'class' => \krok\oauth\TwitterOAuth2::class,
                    'clientId' => '',
                    'clientSecret' => '',
                    'normalizeUserAttributeMap' => [
                        'login' => 'screen_name',
                    ],
                ],
                'gitlab' => [
                    'class' => \krok\oauth\GitLab::class,
                    'clientId' => '',
                    'clientSecret' => '',
                    'normalizeUserAttributeMap' => [
                        'login' => 'username',
                    ],
                ],
                'ok' => [
                    'class' => \krok\oauth\Ok::class,
                    'clientId' => '',
                    'clientSecret' => '',
                    'applicationKey' => '',
                    'normalizeUserAttributeMap' => [
                        'login' => 'uid',
                    ],
                    'scope' => 'VALUABLE_ACCESS,GET_EMAIL',
                ],
            ],
        ],
    ],
```

console

```
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationTable' => '{{%migration}}',
            'interactive' => false,
            'migrationPath' => [
                '@krok/cabinet/migrations',
            ],
        ],
        'access' => [
            'class' => \krok\access\AccessController::class,
            'config' => [
                [
                    'label' => 'Cabinet',
                    'name' => 'cabinet',
                    'controllers' => [
                        'client' => [
                            'label' => 'Client',
                            'actions' => [
                                'index',
                                'create',
                                'update',
                                'delete',
                                'view',
                                'login-as',
                                'refresh-token',
                            ],
                        ],
                        'log' => [
                            'label' => 'Log',
                            'actions' => [
                                'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
```
