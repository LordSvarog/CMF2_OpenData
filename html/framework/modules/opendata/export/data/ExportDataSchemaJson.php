<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\data;

use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\dto\OpendataPropertyDTO;
use yii\helpers\Json;
use yii\web\Response;

class ExportDataSchemaJson implements ExportDataSchemaInterface
{

    /**
     * @var OpendataPassportDTO
     */
    protected $passport;

    /**
     * @var OpendataPropertyDTO[]
     */
    protected $properties = [];

    /**
     * @return string
     */
    public function render(): string
    {
        $json_data['$schema'] = "http://json-schema.org/draft-04/schema#";

        $json_data['title'] = $this->passport->getTitle();
        $json_data['description'] = $this->passport->getDescription();

        $json_data['type'] = 'array';
        $json_data['minItems'] = 1;
        $json_data['items'] = new \stdClass();
        $json_data['items']->description = "Items list";
        $json_data['items']->type = 'object';
        $json_data['items']->properties = new \stdClass();

        foreach ($this->properties as $property) {
            $json_data['items']->properties->{$property->getName()} = new \stdClass();
            $json_data['items']->properties->{$property->getName()}->description = $property->getTitle();
            $json_data['items']->properties->{$property->getName()}->type = $property->getFormat();
        }
        $json_data['items']->properties->required = [];


        $json_data['required'] = [];

        return Json::encode($json_data);
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
     * @param OpendataPropertyDTO $property
     */
    public function addProperty(OpendataPropertyDTO $property)
    {
        array_push($this->properties, $property);
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        return Response::FORMAT_JSON;
    }
}