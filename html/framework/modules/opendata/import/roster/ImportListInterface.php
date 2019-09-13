<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:37
 */

namespace app\modules\opendata\import\roster;

use app\modules\opendata\dto\OpendataListDTO;

interface ImportListInterface
{
    /**
     * @param $data
     *
     * @return OpendataListDTO[]
     */
    public function import(string $data): array;

    /**
     * @param string $delimiter
     *
     * @return void
     */
    public function setDelimiter(string $delimiter);
}