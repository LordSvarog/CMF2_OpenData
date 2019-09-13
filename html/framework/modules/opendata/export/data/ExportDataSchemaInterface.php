<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:37
 */

namespace app\modules\opendata\export\data;

use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\dto\OpendataPropertyDTO;

interface ExportDataSchemaInterface
{
    /**
     * @return string
     */
    public function render(): string;

    /**
     * @param OpendataPassportDTO $passport
     *
     * @return void
     */
    public function loadPassport(OpendataPassportDTO $passport);

    /**
     * @param OpendataPropertyDTO $property
     *
     * @return void
     */
    public function addProperty(OpendataPropertyDTO $property);

    /**
     * @return string
     */
    public function getResponseFormat(): string;
}