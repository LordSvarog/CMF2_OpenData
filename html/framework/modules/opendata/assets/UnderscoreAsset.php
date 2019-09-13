<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 09.09.15
 * Time: 13:05
 */

namespace app\modules\opendata\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class MomentAsset
 *
 * @package app\assets
 */
class UnderscoreAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower/underscore';

    /**
     * @var array
     */
    public $js = [
        'underscore-min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
