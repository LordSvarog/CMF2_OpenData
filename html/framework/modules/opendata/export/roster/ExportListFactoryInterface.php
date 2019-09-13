<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 14:56
 */

namespace app\modules\opendata\export\roster;


interface ExportListFactoryInterface
{
    /**
     * @param $format
     *
     * @return ExportListInterface
     */
    public function create($format): ExportListInterface;

    /**
     * @param $format
     *
     * @return ExportListSchemaInterface
     */
    public function createSchema($format): ExportListSchemaInterface;
}