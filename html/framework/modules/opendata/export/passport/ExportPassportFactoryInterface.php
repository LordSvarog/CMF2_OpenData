<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 14:56
 */

namespace app\modules\opendata\export\passport;


interface ExportPassportFactoryInterface
{
    /**
     * @param $format
     *
     * @return ExportPassportInterface
     */
    public function create($format): ExportPassportInterface;

    /**
     * @param $format
     *
     * @return ExportPassportSchemaInterface
     */
    public function createSchema($format): ExportPassportSchemaInterface;
}