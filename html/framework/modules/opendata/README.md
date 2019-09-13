**Установить и настроить:**

```
composer require contrib/yii2-charts ~0.2.0
```

**Настроить модуль**

config/common.php

```php
'container' => [
    'definitions' => [
        \app\modules\opendata\import\roster\ImportListFactoryInterface::class => \app\modules\opendata\import\roster\ImportListFactory::class,
        \app\modules\opendata\import\passport\ImportPassportFactoryInterface::class => \app\modules\opendata\import\passport\ImportPassportFactory::class,
        \app\modules\opendata\import\data\ImportDataFactoryInterface::class => \app\modules\opendata\import\data\ImportDataFactory::class,
        \app\modules\opendata\export\roster\ExportListFactoryInterface::class => \app\modules\opendata\export\roster\ExportListFactory::class,
        \app\modules\opendata\export\passport\ExportPassportFactoryInterface::class => \app\modules\opendata\export\passport\ExportPassportFactory::class,
        \app\modules\opendata\export\data\ExportDataFactoryInterface::class => \app\modules\opendata\export\data\ExportDataFactory::class,
    ],
],
``` 

config/backend.php

```
    'modules' => [
        'opendata' => [
            'class' => app\modules\opendata\Module::class,
            'viewPath' => '@app/modules/opendata/views/backend',
            'controllerNamespace' => '\app\modules\opendata\controllers\backend',
            'inn' => '7710914971',
        ],
    ],
```

config/frontend.php

```
    'modules' => [
        'opendata' => [
            'class' => app\modules\opendata\Module::class,
            'viewPath' => '@app/modules/opendata/views/frontend',
            'controllerNamespace' => 'app\modules\opendata\controllers\frontend',
            'inn' => '7710914971',
            'email' => [],
            'exportFormats' => ['csv'],
            'exportSchemaFormats' => ['csv'],
        ],
    ],
```

config/console.php

```
    'controllerMap' => [
        // Migrations for the specific project's module
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationTable' => '{{%migration}}',
            'interactive' => false,
            'migrationPath' => [
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
            'importUrl' => 'http://www.rosim.ru/opendata/list.csv', // Список доступных паспортов
        ],
    ],
```

config/frontend/rules.php

```
    [
        'class' => \app\modules\opendata\rules\Opendata::class,
        'pattern' => 'opendata/<path:.+>',
        'route' => '',
    ],
```

config/params.php

```
    'menu' => [
        [
            'label' => 'Open data',
            'items' => [
                [
                    'label' => 'Passport',
                    'url' => ['/opendata/passport'],
                ],
            ],
        ],
    ],
```
