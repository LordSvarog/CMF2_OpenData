<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 08.02.17
 * Time: 23:35
 */

return \yii\helpers\ArrayHelper::merge([
    'name' => 'CMF2',
    'timeZone' => 'UTC',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        \krok\reverseProxy\Bootstrap::class,
    ],
    'aliases' => [
        '@root' => dirname(dirname(__DIR__)),
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@storage' => '@root/storage',
        '@backup' => '@root/backup',
    ],
    'container' => [
        'singletons' => [
            \krok\configure\ConfigureInterface::class => function () {
                $configurable = [
                    \krok\paperdashboard\Configurable::class,
                    \krok\auth\Configurable::class,
                    \krok\mailer\Configurable::class,
                    \krok\sms\Configurable::class,
                    \krok\robots\Configurable::class,
                    \krok\meta\MetaConfigurable::class,
                    \krok\meta\OpenGraphConfigurable::class,
                    \krok\catchAll\Configurable::class,
                    \krok\reverseProxy\Configurable::class,
                ];

                /** @var \krok\configure\serializers\SerializerInterface $serializer */
                $serializer = Yii::createObject(\krok\configure\serializers\SerializerInterface::class);

                return new \krok\configure\Configure($configurable, $serializer);
            },
        ],
        'definitions' => [
            \krok\queue\mailer\MailerJob::class => [
                'mailer' => 'sender',
            ],
            \yii\mail\MailerInterface::class => function () {
                return Yii::$app->getMailer();
            },
            \krok\tinymce\uploader\actions\UploaderAction::class => [
                'scheme' => 'editor://',
            ],
            \krok\filesystem\GridBuilderInterface::class => \krok\filesystem\GridBuilder::class,
            \League\Flysystem\MountManager::class => function (\yii\di\Container $container) {

                $cache = new \League\Flysystem\Filesystem(new \krok\filesystem\adapter\Local(Yii::getAlias('@storage/cache')));

                $storage = new \League\Flysystem\Filesystem(new \krok\filesystem\adapter\Local(Yii::getAlias('@storage/storage')));
                $storage->addPlugin(new \krok\filesystem\plugins\PublicUrl('getRenderUrl',
                    $container->get(\krok\glide\UrlBuilderManager::class)->getUrlBuilder('render/storage')));
                $storage->addPlugin(new \krok\filesystem\plugins\PublicUrl('getAttachmentUrl',
                    $container->get(\krok\glide\UrlBuilderManager::class)->getUrlBuilder('attachment/storage')));

                $editor = new \League\Flysystem\Filesystem(new \krok\filesystem\adapter\Local(Yii::getAlias('@storage/editor')));
                $editor->addPlugin(new \krok\filesystem\plugins\PublicUrl('getRenderUrl',
                    $container->get(\krok\glide\UrlBuilderManager::class)->getUrlBuilder('render/editor')));
                $editor->addPlugin(new \krok\filesystem\plugins\PublicUrl('getAttachmentUrl',
                    $container->get(\krok\glide\UrlBuilderManager::class)->getUrlBuilder('attachment/editor')));

                $system = new \League\Flysystem\Filesystem(new \krok\filesystem\adapter\Local(Yii::getAlias('@storage/system')));
                $system->addPlugin(new \krok\filesystem\plugins\PublicUrl('getAttachmentUrl',
                    $container->get(\krok\glide\UrlBuilderManager::class)->getUrlBuilder('attachment/system')));

                $mount = new \League\Flysystem\MountManager([
                    'cache' => $cache,
                    'storage' => $storage,
                    'editor' => $editor,
                    'system' => $system,
                ]);

                $mount->addPlugin(new \krok\filesystem\plugins\Content('getContent'));

                return $mount;
            },
            \krok\glide\actions\GlideAction::class => [
                'signature' => hash('crc32', __FILE__),
            ],
            \krok\glide\UrlBuilderManager::class => function () {
                $signature = hash('crc32', __FILE__);

                $urlBuilder = new \krok\glide\UrlBuilderManager([
                    'render/storage' => \League\Glide\Urls\UrlBuilderFactory::create('render/storage', $signature),
                    'attachment/storage' => \League\Glide\Urls\UrlBuilderFactory::create('attachment/storage'),

                    'render/editor' => \League\Glide\Urls\UrlBuilderFactory::create('render/editor', $signature),
                    'attachment/editor' => \League\Glide\Urls\UrlBuilderFactory::create('attachment/editor'),

                    'attachment/system' => \League\Glide\Urls\UrlBuilderFactory::create('attachment/system'),
                ]);

                return $urlBuilder;
            },
            \krok\glide\ServerManager::class => function (\yii\di\Container $container) {
                /** @var \League\Flysystem\MountManager $mount */
                $mount = $container->get(\League\Flysystem\MountManager::class);

                $cache = $mount->getFilesystem('cache');
                $driver = 'imagick';

                $servers = new \krok\glide\ServerManager([
                    'storage' => \League\Glide\ServerFactory::create([
                        'source' => $mount->getFilesystem('storage'),
                        'base_url' => 'render/storage',
                        'cache' => $cache,
                        'driver' => $driver,
                    ]),
                    'editor' => \League\Glide\ServerFactory::create([
                        'source' => $mount->getFilesystem('editor'),
                        'base_url' => 'render/editor',
                        'cache' => $cache,
                        'driver' => $driver,
                    ]),
                ]);

                return $servers;
            },
            \League\Tactician\CommandBus::class => function () {
                $lockingMiddleware = new \League\Tactician\Plugins\LockingMiddleware();
                $commandMiddleware = new \League\Tactician\Handler\CommandHandlerMiddleware(
                    new \League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor(),
                    new \League\Tactician\Handler\Locator\InMemoryLocator([]),
                    new \League\Tactician\Handler\MethodNameInflector\InvokeInflector()
                );

                return new \League\Tactician\CommandBus([$lockingMiddleware, $commandMiddleware]);
            },
            \krok\language\LanguageInterface::class => function () {
                $list = [
                    [
                        'iso' => 'ru-RU',
                        'title' => 'Русский',
                    ],
                ];

                return Yii::createObject(\krok\language\Language::class, [$list]);
            },
            \krok\configure\helpers\ConfigureHelperInterface::class => \krok\configure\helpers\ConfigureHelper::class,
            \krok\configure\serializers\SerializerInterface::class => \krok\configure\serializers\JsonSerializer::class,
            \krok\meta\serializers\SerializerInterface::class => \krok\meta\serializers\JsonSerializer::class,
            \krok\meta\MetaInterface::class => \krok\meta\Meta::class,
            \krok\sms\GateInterface::class => \krok\queue\sms\Gate::class,
        ],
    ],
    'modules' => [
        'content' => [
            'class' => \krok\content\Module::class,
            'layouts' => [
                '//index' => 'Главная',
                '//common' => 'Типовая',
            ],
            'views' => [
                'index' => 'Главная',
                'about' => 'О нас',
            ],
        ],
        'glide' => [
            'class' => \yii\base\Module::class,
            'controllerNamespace' => 'krok\glide\controllers',
        ],
        'filesystem' => [
            'class' => \yii\base\Module::class,
            'controllerNamespace' => 'krok\filesystem\controllers',
        ],
    ],
    'components' => [
        'formatter' => [
            'class' => \yii\i18n\Formatter::class,
            'timeZone' => 'Europe/Moscow',
            'numberFormatterSymbols' => [
                \NumberFormatter::CURRENCY_SYMBOL => 'руб.',
            ],
            'numberFormatterOptions' => [
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
            ],
        ],
        'security' => [
            'class' => \yii\base\Security::class,
            'passwordHashCost' => 15,
        ],
        'session' => [
            'class' => \yii\web\CacheSession::class,
            'timeout' => 24 * 60 * 60,
            'cache' => [
                'class' => \yii\redis\Cache::class,
                'defaultDuration' => 0,
                'keyPrefix' => hash('crc32', __FILE__),
                'redis' => [
                    'hostname' => env('REDIS_HOST'),
                    'port' => env('REDIS_PORT'),
                    'database' => 1,
                ],
            ],
        ],
        'cache' => [
            'class' => \yii\redis\Cache::class,
            'defaultDuration' => 24 * 60 * 60,
            'keyPrefix' => hash('crc32', __FILE__),
            'redis' => [
                'hostname' => env('REDIS_HOST'),
                'port' => env('REDIS_PORT'),
                'database' => 0,
            ],
        ],
        'mailer' => [
            'class' => \krok\queue\mailer\Mailer::class,
            'messageClass' => \krok\mailer\Message::class,
        ],
        'sender' => [
            'class' => \krok\mailer\Mailer::class,
            'transport' => [
                'class' => \krok\mailer\Swift_SmtpTransport::class,
            ],
        ],
        'i18n' => [
            'class' => \yii\i18n\I18N::class,
            'translations' => [
                'yii' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@app/messages',
                ],
                'app' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@app/messages',
                ],
                'system' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'log' => [
            'class' => \yii\log\Dispatcher::class,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => [
                        'error',
                        'warning',
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                    ],
                    'enabled' => YII_ENV_PROD,
                ],
                'sentry' => [
                    'class' => \krok\sentry\log\SentryTarget::class,
                    'levels' => \yii\log\Logger::LEVEL_ERROR | \yii\log\Logger::LEVEL_WARNING,
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                    ],
                ],
            ],
        ],
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'channel' => hash('crc32', __FILE__),
            'redis' => [
                'hostname' => env('REDIS_HOST'),
                'port' => env('REDIS_PORT'),
                'database' => 2,
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => require(__DIR__ . '/params.php'),
],
    file_exists(__DIR__ . '/local.php') ? require(__DIR__ . '/local.php') : []);
