<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 03.08.17
 * Time: 10:47
 */

namespace app\modules\opendata\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class OpendataBackAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/opendata/assets/dist';

    /**
     * @var array
     */
    public $css = [
        'opendata.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'opendata-backend.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class,
    ];
}
