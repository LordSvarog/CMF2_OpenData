<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:37
 */

namespace app\modules\opendata\import\data;

use app\modules\opendata\dto\OpendataDataDTO;
use app\modules\opendata\dto\PassportSchemaDTO;

interface ImportDataInterface
{
    /**
     * @param $data string
     *
     * @return OpendataDataDTO[]|array
     */
    public function import(string $data): array;

    /**
     * @param $data string
     *
     * @return PassportSchemaDTO
     */
    public function importSchema(string $data): PassportSchemaDTO;

    /**
     * @param PassportSchemaDTO $schema
     *
     * @return void
     */
    public function setSchema(PassportSchemaDTO $schema);

    /**
     * @return PassportSchemaDTO
     */
    public function getSchema(): PassportSchemaDTO;

    /**
     * @param string $delimiter
     *
     * @return void
     */
    public function setDelimiter(string $delimiter);
}