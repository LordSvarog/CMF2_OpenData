<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 09.02.17
 * Time: 16:31
 */

return [
    /**
     * Glide
     */
    'render/<scheme:[\w]+>/<path:[\w\/\.]+>' => 'glide/default/render',
    /**
     * Filesystem
     */
    'attachment/<scheme:[\w]+>/<path:[\w\/\.\-]+>' => 'filesystem/default/attachment',
    /**
     * Content
     */
    '<language:\w+\-\w+>/content/<alias:[\w\-]+>' => 'content/default/index',
    /**
     * Robots
     */
    [
        'pattern' => 'robots',
        'route' => 'robots',
        'suffix' => '.txt',
    ],
    /**
     * System
     */
    '<language:\w+\-\w+>' => '/',
    '<language:\w+\-\w+>/<module:[\w\-]+>' => '<module>',
    '<language:\w+\-\w+>/<module:[\w\-]+>/<controller:[\w\-]+>' => '<module>/<controller>',
    '<language:\w+\-\w+>/<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>/<page:\d+>/<per-page:\d+>' => '<module>/<controller>/<action>',
    '<language:\w+\-\w+>/<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<module>/<controller>/<action>',
    '<language:\w+\-\w+>/<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
    [
        'class' => \app\modules\opendata\rules\Opendata::class,
        'pattern' => 'opendata/<path:.+>',
        'route' => '',
    ],
];
