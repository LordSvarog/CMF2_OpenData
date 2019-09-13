<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:37
 */

namespace app\modules\opendata\export\roster;

interface ExportListSchemaInterface
{
    /**
     * @return string
     */
    public function render(): string;

    /**
     * @return string
     */
    public function getResponseFormat(): string;
}