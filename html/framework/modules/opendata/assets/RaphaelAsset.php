<?php
/**
 * Created by PhpStorm.
 * User: zima
 * Date: 05.11.18
 * Time: 14:14
 */

namespace app\modules\opendata\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class RaphaelAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot/static/default';

    /**
     * @var string
     */
    public $baseUrl = '@web/static/default';

    /**
     * @var array
     */
    public $js = [
        'js/raphael/raphael.min.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class,
    ];
}
