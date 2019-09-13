<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:37
 */

namespace app\modules\opendata\export\roster;

use app\modules\opendata\dto\OpendataListDTO;

interface ExportListInterface
{
    /**
     * @return string
     */
    public function render(): string;

    /**
     * @param OpendataListDTO $item
     *
     * @return void
     */
    public function addItem(OpendataListDTO $item);

    /**
     * @return string
     */
    public function getResponseFormat(): string;
}