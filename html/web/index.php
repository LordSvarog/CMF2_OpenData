<?php

ini_set('session.use_strict_mode', true);
ini_set('session.sid_length', 128);
ini_set('session.sid_bits_per_character', 6);

require(__DIR__ . '/../framework/vendor/autoload.php');

require(__DIR__ . '/../framework/config/env.php');
require(__DIR__ . '/../framework/config/sentry.php');

require(__DIR__ . '/../framework/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../framework/config/frontend.php');

(new \krok\application\web\Application($config))->run();
