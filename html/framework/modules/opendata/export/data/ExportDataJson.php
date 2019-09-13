<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\data;

use app\modules\opendata\dto\OpendataDataDTO;
use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\dto\PassportSchemaDTO;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\Response;

class ExportDataJson implements ExportDataInterface
{
    /**
     * @var OpendataPassportDTO
     */
    protected $passport;

    /**
     * @var PassportSchemaDTO
     */
    protected $schema;

    /**
     * @var OpendataDataDTO[]
     */
    protected $list = [];

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        if (!$this->schema) {
            throw new Exception('Passport schema is not loaded');
        }

        if (!$this->passport) {
            throw new Exception('Passport is not loaded');
        }

        $json_data = [];
        if (!empty($this->list)) {
            foreach ($this->list as $item) {
                $stdObject = new \stdClass();
                foreach ($this->schema->getProperties() as $propertyDTO) {
                    $value = $item->getPropertyValue($propertyDTO->getName());
                    $stdObject->{$propertyDTO->getName()} = $value;
                }
                array_push($json_data, $stdObject);
            }
        }

        return Json::encode($json_data);
    }


    /**
     * @param PassportSchemaDTO $schema
     */
    public function loadSchema(PassportSchemaDTO $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param OpendataPassportDTO $passport
     *
     * @return void
     */
    public function loadPassport(OpendataPassportDTO $passport)
    {
        $this->passport = $passport;
    }

    /**
     * @param OpendataDataDTO $item
     */
    public function addItem(OpendataDataDTO $item)
    {
        array_push($this->list, $item);
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        return Response::FORMAT_JSON;
    }

}