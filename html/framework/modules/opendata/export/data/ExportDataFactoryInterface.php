<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 14:56
 */

namespace app\modules\opendata\export\data;


interface ExportDataFactoryInterface
{
    /**
     * @param $format
     *
     * @return ExportDataInterface
     */
    public function create($format): ExportDataInterface;

    /**
     * @param $format
     *
     * @return ExportDataSchemaInterface
     */
    public function createSchema($format): ExportDataSchemaInterface;
}