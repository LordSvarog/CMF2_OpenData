<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 03.08.17
 * Time: 13:00
 */

namespace app\modules\opendata\helper;

use yii\helpers\StringHelper;

/**
 * Class ConvertEntitiesHelper
 *
 * @package app\modules\opendata\helper
 */
class ConvertEntitiesHelper extends StringHelper
{
    /**
     * convert string to UTF-8 entities
     *
     * @param $str
     *
     * @return string
     */
    public static function convert($str)
    {
        return mb_encode_numericentity($str, [0x0, 0xffff, 0, 0xffff], "UTF-8");
    }
}
