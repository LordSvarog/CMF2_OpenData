<?php

$config = [
    'id' => 'web',
    'defaultRoute' => 'system',
    'as accessControl' => [
        'class' => \krok\system\components\backend\AccessControl::class,
        'except' => [
            'gii/*',
            'debug/*',
            'auth/default/login',
            'auth/default/captcha',
        ],
    ],
    'bootstrap' => [
        \krok\reverseProxy\Bootstrap::class,
    ],
    'aliases' => [
        '@themes' => '@vendor/yii2-developer/yii2-paper-dashboard/src',
    ],
    'container' => [
        'definitions' => [
            \yii\captcha\CaptchaAction::class => [
                'transparent' => true,
            ],
            \yii\grid\ActionColumn::class => [
                'header' => 'Действие',
                'options' => [
                    'width' => 150,
                ],
            ],
            \yii\grid\DataColumn::class => \krok\grid\DataColumn::class,
            \krok\grid\DatePickerColumn::class => [
                'format' => 'datetime',
            ],
            \krok\grid\DatetimePickerColumn::class => [
                'dateFormat' => 'Y-m-d H:i',
            ],
            \krok\flatpickr\FlatpickrDatetimeWidget::class => [
                'class' => \krok\datetimeFormatter\FlatpickrDatetimeWidget::class,
                'dateFormat' => 'Y-m-d H:i',
                'defaultDatetime' => true,
            ],
            \krok\flatpickr\FlatpickrDateWidget::class => \krok\datetimeFormatter\FlatpickrDateWidget::class,
            \krok\flatpickr\FlatpickrTimeWidget::class => \krok\datetimeFormatter\FlatpickrTimeWidget::class,
            \krok\datetimeFormatter\validators\DatetimeFormatterValidator::class => [
                'timestampAttributeFormat' => 'yyyy-MM-dd HH:mm',
            ],
            \krok\editor\EditorWidget::class => \krok\tinymce\TinyMceWidget::class,
            \krok\tinymce\TinyMceWidget::class => [
                'clientOptions' => [
                    'branding' => false,
                    'menubar' => false,
                    'language' => 'ru',
                    'height' => 600,
                    'plugins' => [
                        'advlist',
                        'anchor',
                        'charmap',
                        'code',
                        'textcolor',
                        'colorpicker',
                        'media',
                        'image',
                        'hr',
                        'insertdatetime',
                        'link',
                        'lists',
                        'nonbreaking',
                        'paste',
                        'print',
                        'searchreplace',
                        'spellchecker',
                        'table',
                        'template',
                        'visualblocks',
                        'visualchars',
                        // passive
                        'autolink',
                        'contextmenu',
                        'imagetools',
                        'wordcount',
                    ],
                    'external_plugins' => [
                        'easyfileupload' => 'easyfileupload',
                    ],
                    'toolbar1' => implode(' | ', [
                        'formatselect fontselect fontsizeselect',
                    ]),
                    'toolbar2' => implode(' | ', [
                        'bold italic underline strikethrough',
                        'subscript superscript',
                        'alignleft aligncenter alignright alignjustify',
                        'outdent indent',
                        'forecolor backcolor',
                    ]),
                    'toolbar3' => implode(' | ', [
                        'searchreplace',
                        'cut copy paste',
                        'table',
                        'numlist bullist',
                        'link unlink',
                        'easyfileupload image media',
                        'hr',
                        'blockquote',
                        'insertdatetime',
                        'anchor',
                        'charmap',
                        'nonbreaking',
                        'template',
                    ]),
                    'toolbar4' => implode(' | ', [
                        'code',
                        'undo redo',
                        'visualblocks visualchars',
                        'removeformat',
                        'spellchecker',
                        'print',
                    ]),
                    'relative_urls' => false,
                    'images_upload_url' => '/cp/tinymce/uploader/default/image',
                    'easyfileupload_url' => '/cp/tinymce/uploader/default/file',
                    'insertdatetime_formats' => [
                        '%H:%M',
                        '%d.%m.%Y',
                    ],
                    'templates' => [
                        [
                            'title' => 'NSign',
                            'description' => 'NSign',
                            'content' => '<a href="http://www.nsign.ru" target="_blank">NSign</a>',
                        ],
                    ],
                    'spellchecker_languages' => 'Russian=ru,English=en',
                    'spellchecker_language' => 'ru',
                    'spellchecker_rpc_url' => '//speller.yandex.net/services/tinyspell',
                ],
            ],
            \krok\backup\actions\FilesystemJobAction::class => function (
                \yii\di\Container $container,
                array $configure
            ) {
                [$id, $controller] = $configure;

                /** @var \krok\BackupManager\FilesystemManager $manager */
                $manager = $container->get(\krok\BackupManager\FilesystemManager::class);

                $action = new \krok\backup\actions\FilesystemJobAction($id, $controller, $manager);

                $action->destinations = [
                    new \BackupManager\Filesystems\Destination('filesystem',
                        (new DateTime())->format('Y-m-d_H:i:s') . '.zip'),
                ];

                return $action;
            },
            \krok\backup\actions\DatabaseJobAction::class => function (
                \yii\di\Container $container,
                array $configure
            ) {
                [$id, $controller] = $configure;

                /** @var \krok\BackupManager\DatabaseManager $manager */
                $manager = $container->get(\krok\BackupManager\DatabaseManager::class);

                $action = new \krok\backup\actions\DatabaseJobAction($id, $controller, $manager);

                $action->destinations = [
                    new \BackupManager\Filesystems\Destination('database',
                        (new DateTime())->format('Y-m-d_H:i:s') . '.sql'),
                ];

                return $action;
            },
            \krok\BackupManager\Finders\FinderProvider::class => function () {
                $finder = new \krok\BackupManager\Finders\FinderProvider(new \BackupManager\Config\Config([
                    'symfony' => [
                        'type' => 'symfony',
                        'exclude' => [
                            'web/cp/assets',
                            'web/assets',
                            'storage',
                            'backup',
                        ],
                        'root' => Yii::getAlias('@root'),
                    ],
                ]));
                $finder->add(new \krok\BackupManager\Finders\SymfonyFinder());

                return $finder;
            },
            \BackupManager\Databases\DatabaseProvider::class => function () {
                $databases = new \BackupManager\Databases\DatabaseProvider(new \BackupManager\Config\Config([
                    'db' => [
                        'type' => 'mysql',
                        'host' => env('MYSQL_HOST'),
                        'port' => env('MYSQL_PORT'),
                        'user' => env('MYSQL_USER'),
                        'pass' => env('MYSQL_PASSWORD'),
                        'database' => env('MYSQL_DATABASE'),
                        'singleTransaction' => true,
                    ],
                ]));
                $databases->add(new \BackupManager\Databases\MysqlDatabase());

                return $databases;
            },
            \BackupManager\Filesystems\FilesystemProvider::class => function () {
                $filesystems = new \BackupManager\Filesystems\FilesystemProvider(new \BackupManager\Config\Config([
                    'local' => [
                        'type' => 'Local',
                        'root' => Yii::getAlias('@backup/tmp'),
                    ],
                    'filesystem' => [
                        'type' => 'Local',
                        'root' => Yii::getAlias('@backup/filesystem'),
                    ],
                    'database' => [
                        'type' => 'Local',
                        'root' => Yii::getAlias('@backup/database'),
                    ],
                ]));
                $filesystems->add(new \BackupManager\Filesystems\LocalFilesystem());

                return $filesystems;
            },
            \BackupManager\Compressors\CompressorProvider::class => function () {
                $compressors = new \BackupManager\Compressors\CompressorProvider();
                $compressors->add(new \BackupManager\Compressors\GzipCompressor());

                return $compressors;
            },
            \krok\paperdashboard\widgets\welcome\WelcomeProvider::class => \krok\auth\WelcomeProvider::class,
        ],
    ],
    'modules' => [
        'system' => [
            'class' => \krok\system\Module::class,
            'viewPath' => '@krok/system/views/backend',
            'controllerNamespace' => 'krok\system\controllers\backend',
        ],
        'tinymce' => [
            'class' => \yii\base\Module::class,
            'modules' => [
                'uploader' => [
                    'class' => \yii\base\Module::class,
                    'controllerNamespace' => 'krok\tinymce\uploader\controllers\backend',
                ],
            ],
        ],
        'auth' => [
            'class' => \krok\auth\Module::class,
            'viewPath' => '@krok/auth/views/backend',
            'controllerNamespace' => 'krok\auth\controllers\backend',
        ],
        'content' => [
            'viewPath' => '@krok/content/views/backend',
            'controllerNamespace' => 'krok\content\controllers\backend',
        ],
        'backup' => [
            'class' => \yii\base\Module::class,
            'viewPath' => '@krok/backup/views/backend',
            'controllerNamespace' => 'krok\backup\controllers\backend',
        ],
        'configure' => [
            'class' => \krok\configure\Module::class,
            'viewPath' => '@krok/configure/views/backend',
            'controllerNamespace' => 'krok\configure\controllers\backend',
        ],
    ],
    'components' => [
        'view' => [
            'class' => \yii\web\View::class,
            'theme' => [
                'class' => \yii\base\Theme::class,
                'basePath' => '@themes',
                'baseUrl' => '@themes',
                'pathMap' => [
                    '@krok/system/views/backend' => '@app/modules/system/views/backend',
                    '@krok/system/views/backend/layouts' => '@themes/views/layouts',
                ],
            ],
        ],
        'urlManager' => [
            'class' => \yii\di\ServiceLocator::class,
            'components' => [
                'default' => require(__DIR__ . '/backend/urlManager.php'),
                'frontend' => require(__DIR__ . '/frontend/urlManager.php'),
            ],
        ],
        'assetManager' => [
            'class' => \yii\web\AssetManager::class,
            'appendTimestamp' => true,
            'dirMode' => 0755,
            'fileMode' => 0644,
            'bundles' => [
                \yii\web\JqueryAsset::class => [
                    'js' => [
                        YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js',
                    ],
                ],
                \yii\bootstrap\BootstrapAsset::class => [
                    'css' => [
                        YII_ENV_DEV ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                    ],
                ],
                \yii\bootstrap\BootstrapPluginAsset::class => [
                    'js' => [
                        YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                    ],
                ],
                \krok\paperdashboard\assets\PaperdashboardAsset::class => [
                    'depends' => [
                        \yii\web\JqueryAsset::class,
                        \yii\bootstrap\BootstrapAsset::class,
                        \krok\paperdashboard\assets\BootstrapSwitchTagsAsset::class,
                        \krok\paperdashboard\assets\Es6PromiseAutoAsset::class,
                        \krok\paperdashboard\assets\PerfectScrollbarAsset::class,
                        \krok\bootbox\BootBoxAsset::class,
                    ],
                ],
            ],
        ],
        'session' => [
            'name' => 'PHPSESSID-CP',
            'cookieParams' => [
                'httponly' => true,
                'path' => '/cp',
            ],
        ],
        'request' => [
            'class' => \krok\language\Request::class,
            'csrfParam' => '_csrf-CP',
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/cp',
            ],
            'cookieValidationKey' => env('YII_COOKIE_VALIDATION_KEY'),
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
            'cache' => 'cache',
        ],
        'user' => [
            'class' => \krok\auth\components\User::class,
            'identityClass' => \krok\auth\models\Auth::class,
            'loginUrl' => ['/auth/default/login'],
            'on afterLogin' => function (\yii\web\UserEvent $event) {
                \krok\catchAll\UserHandler::loginHandle();
                \krok\auth\components\UserEventHandler::handleAfterLogin($event);
            },
            'on afterLogout' => function (\yii\web\UserEvent $event) {
                \krok\catchAll\UserHandler::logoutHandle();
                \krok\auth\components\UserEventHandler::handleAfterLogout($event);
            },
        ],
        'errorHandler' => [
            'class' => \yii\web\ErrorHandler::class,
            'errorAction' => 'system/default/error',
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        'panels' => [
            'queue' => [
                'class' => \yii\queue\debug\Panel::class,
            ],
        ],
        'allowedIPs' => [
            '*',
        ],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        'generators' => [
            'module' => [
                'class' => \yii\gii\generators\module\Generator::class,
                'messageCategory' => 'system',
                'templates' => [
                    'paper-dashboard' => '@themes/gii/module',
                ],
                'template' => 'paper-dashboard',
            ],
            'model' => [
                'class' => \yii\gii\generators\model\Generator::class,
                'generateQuery' => true,
                'useTablePrefix' => true,
                'messageCategory' => 'system',
                'templates' => [
                    'paper-dashboard' => '@themes/gii/model',
                ],
                'template' => 'paper-dashboard',
            ],
            'crud' => [
                'class' => \yii\gii\generators\crud\Generator::class,
                'enableI18N' => true,
                'baseControllerClass' => \krok\system\components\backend\Controller::class,
                'messageCategory' => 'system',
                'templates' => [
                    'paper-dashboard' => '@themes/gii/crud',
                ],
                'template' => 'paper-dashboard',
            ],
            'job' => [
                'class' => \yii\queue\gii\Generator::class,
            ],
        ],
        'allowedIPs' => [
            '127.0.0.1',
            '::1',
            '172.72.*.*',
        ],
    ];
}

return \yii\helpers\ArrayHelper::merge(require(__DIR__ . '/common.php'), $config);
