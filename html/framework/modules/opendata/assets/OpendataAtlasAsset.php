<?php
/**
 * Created by PhpStorm.
 * User: zima
 * Date: 05.11.18
 * Time: 11:36
 */

namespace app\modules\opendata\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class OpendataAtlasAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/opendata/assets/dist/atlas';

    /**
     * @var array
     */
    public $css = [
        'css/atlas.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'js/paths.js',
        'js/jquery.atlas.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class,
        UnderscoreAsset::class,
        RaphaelAsset::class,
    ];

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV,
    ];
}
