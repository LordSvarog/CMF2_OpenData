<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:37
 */

namespace app\modules\opendata\export\passport;

use app\modules\opendata\dto\OpendataPassportDTO;

interface ExportPassportInterface
{
    /**
     * @return string
     */
    public function render(): string;

    /**
     * @param OpendataPassportDTO $item
     *
     * @return void
     */
    public function load(OpendataPassportDTO $item);

    /**
     * @return string
     */
    public function getResponseFormat(): string;
}