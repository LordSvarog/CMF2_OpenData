<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:37
 */

namespace app\modules\opendata\export\data;

use app\modules\opendata\dto\OpendataDataDTO;
use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\dto\PassportSchemaDTO;
use yii\base\Exception;

interface ExportDataInterface
{
    /**
     * @return string
     * @throws Exception
     */
    public function render(): string;

    /**
     * @param PassportSchemaDTO $schema
     *
     * @return void
     */
    public function loadSchema(PassportSchemaDTO $schema);

    /**
     * @param OpendataPassportDTO $passport
     *
     * @return void
     */
    public function loadPassport(OpendataPassportDTO $passport);

    /**
     * @param OpendataDataDTO $item
     *
     * @return void
     */
    public function addItem(OpendataDataDTO $item);

    /**
     * @return string
     */
    public function getResponseFormat(): string;
}