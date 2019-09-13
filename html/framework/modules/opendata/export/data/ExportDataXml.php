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
use yii\web\Response;

class ExportDataXml implements ExportDataInterface
{

    /**
     * @var OpendataDataDTO[]
     */
    protected $list = [];

    /**
     * @var OpendataPassportDTO
     */
    protected $passport;

    /**
     * @var PassportSchemaDTO
     */
    protected $schema;

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

        $dom = new \DOMDocument('1.0', 'utf-8');
        $list = $dom->createElement($this->passport->getCode());

        $list->setAttribute('xmlns', 'http://www.w3schools.com');
        $list->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $list->setAttribute('xsi:schemaLocation', 'http://www.w3schools.com ' . $this->passport->getSchemaUrl());
        if (!empty($this->list)) {
            foreach ($this->list as $item) {
                $itemNode = $dom->createElement($this->passport->getCode() . '_item');
                foreach ($this->schema->getProperties() as $propertyDTO) {
                    $value = $item->getPropertyValue($propertyDTO->getName());
                    if (preg_match('/\&|#/', $value)) {
                        $cdata = $dom->createCDATASection($value);
                        $elem = $dom->createElement($propertyDTO->getName());
                        $elem->appendChild($cdata);
                        $itemNode->appendChild($elem);
                        unset($cdata);
                        unset($elem);
                    } else {
                        $itemNode->appendChild($dom->createElement($propertyDTO->getName(), $value));
                    }

                }
                $list->appendChild($itemNode);
            }
        }

        $dom->appendChild($list);

        return $dom->saveXML();
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
        return Response::FORMAT_XML;
    }
}