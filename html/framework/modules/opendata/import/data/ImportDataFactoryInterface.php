<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 14:56
 */

namespace app\modules\opendata\import\data;

/**
 * Interface ImportDataFactoryInterface
 *
 * @package app\modules\opendata\import\data
 */
interface ImportDataFactoryInterface
{
    /**
     * @param string $format
     *
     * @return ImportDataInterface
     */
    public function create(string $format): ImportDataInterface;
}
