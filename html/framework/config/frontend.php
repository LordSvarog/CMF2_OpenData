<?php

$config = [
    'id' => 'web',
    'defaultRoute' => 'content/default/index',
    'bootstrap' => [
        \krok\catchAll\Bootstrap::class,
        \krok\reverseProxy\Bootstrap::class,
    ],
    'container' => [
        'definitions' => [
            \krok\robots\RobotsInterface::class => [
                'class' => \krok\robots\Robots::class,
                'path' => '@runtime/robots/robots.txt',
                'lines' => [
                    'User-agent: *',
                ],
                'generators' => [
                    \krok\robots\generators\AllowGenerator::class,
                ],
            ],
        ],
    ],
    'modules' => [
        'content' => [
            'viewPath' => '@krok/content/views/frontend',
            'controllerNamespace' => 'krok\content\controllers\frontend',
        ],
        'robots' => [
            'class' => \yii\base\Module::class,
            'controllerNamespace' => 'krok\robots\controllers\frontend',
        ],
        'catchAll' => [
            'class' => \yii\base\Module::class,
            'viewPath' => '@krok/catchAll/views/frontend',
            'controllerNamespace' => 'krok\catchAll\controllers\frontend',
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
            ],
        ],
        'request' => [
            'class' => \krok\language\Request::class,
            'cookieValidationKey' => env('YII_COOKIE_VALIDATION_KEY'),
        ],
        'errorHandler' => [
            'class' => \yii\web\ErrorHandler::class,
            'errorAction' => 'content/default/error',
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        'panels' => [
            'user' => [
                'class' => \yii\debug\panels\UserPanel::class,
                'ruleUserSwitch' => [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
            'queue' => [
                'class' => \yii\queue\debug\Panel::class,
            ],
        ],
        'allowedIPs' => [
            '*',
        ],
    ];
}

return \yii\helpers\ArrayHelper::merge(require(__DIR__ . '/common.php'), $config);
