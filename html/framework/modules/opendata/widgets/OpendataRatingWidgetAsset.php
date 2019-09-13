<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 03.08.17
 * Time: 10:47
 */

namespace app\modules\opendata\widgets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class OpendataRatingWidgetAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/opendata/widgets/assets';

    /**
     * @var array
     */
    public $js = [
        'starwarsjs.js',
        'opendata-rate.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class,
    ];
}
