<?php

$config = [
    'id' => 'console',
    'bootstrap' => [
        'queue',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationTable' => '{{%migration}}',
            'useTablePrefix' => true,
            'interactive' => false,
            'migrationPath' => [
                '@app/migrations',
                '@yii/rbac/migrations',
                '@krok/auth/migrations',
                '@krok/content/migrations',
                '@krok/configure/migrations',
                '@krok/meta/migrations',
                '@app/modules/opendata/migrations',
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
                    'label' => 'System',
                    'name' => 'system',
                    'controllers' => [
                        'default' => [
                            'label' => 'System',
                            'actions' => [
                                'error',
                                'index',
                                'flush-cache',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Tinymce',
                    'name' => 'tinymce/uploader',
                    'controllers' => [
                        'default' => [
                            'label' => 'Tinymce',
                            'actions' => [
                                'image',
                                'file',
                            ],
                        ],
                    ],
                ],
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
                [
                    'label' => 'Content',
                    'name' => 'content',
                    'controllers' => [
                        'default' => [
                            'label' => 'Content',
                            'actions' => [
                                'index',
                                'create',
                                'update',
                                'delete',
                                'view',
                                'transliterate',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Backup',
                    'name' => 'backup',
                    'controllers' => [
                        'default' => [
                            'label' => 'Backup',
                            'actions' => ['index'],
                        ],
                        'make' => [
                            'label' => 'Make backup',
                            'actions' => [
                                'filesystem',
                                'database',
                            ],
                        ],
                        'download' => [
                            'label' => 'Download backup',
                            'actions' => [
                                'filesystem',
                                'database',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Configure',
                    'name' => 'configure',
                    'controllers' => [
                        'default' => [
                            'label' => 'Configure',
                            'actions' => [
                                'list',
                                'save',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Open Data',
                    'name' => 'opendata',
                    'controllers' => [
                        'passport' => [
                            'label' => 'Passport',
                            'actions' => [],
                        ],
                        'set' => [
                            'label' => 'Set',
                            'actions' => [
                                'create',
                                'update',
                                'delete',
                                'import',
                                'data',
                                'delete-data',
                                'chart',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'modules' => [
        'opendata' => [
            'class' => app\modules\opendata\Module::class,
            'controllerNamespace' => 'app\modules\opendata\controllers\console',
            'inn' => '7710914971',
            // Список доступных паспортов
            'importUrl' => 'http://www.rosim.ru/opendata/list.csv',
        ],
    ],
    'components' => [
        'urlManager' => [
            'class' => \yii\di\ServiceLocator::class,
            'components' => [
                'default' => require(__DIR__ . '/frontend/urlManager.php'),
                'backend' => require(__DIR__ . '/backend/urlManager.php'),
            ],
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
            'cache' => 'cache',
        ],
        'errorHandler' => [
            'class' => \yii\console\ErrorHandler::class,
        ],
    ],
];

return \yii\helpers\ArrayHelper::merge(require(__DIR__ . '/common.php'), $config);
