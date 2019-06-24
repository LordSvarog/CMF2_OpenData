<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 24.06.19
 * Time: 11:15
 */

\Sentry\init([
    'dsn' => filter_var(env('SENTRY_DSN'), FILTER_VALIDATE_URL) ? env('SENTRY_DSN') : null,
    'environment' => YII_ENV,
]);
