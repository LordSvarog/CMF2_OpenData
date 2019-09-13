<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\passport;

use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\Module;
use Yii;
use yii\web\Response;

class ExportPassportXml implements ExportPassportInterface
{

    /**
     * @var OpendataPassportDTO
     */
    protected $item;

    /**
     * @var Module
     */
    protected $module;

    /**
     * @return string
     */
    public function render(): string
    {
        $this->module = Yii::$app->getModule('opendata');

        $dom = new \DOMDocument('1.0', 'utf-8');

        $meta = $dom->createElement('meta');
        $meta->setAttribute('xmlns', 'http://www.w3schools.com');
        $meta->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $meta->setAttribute('xsi:schemaLocation', 'http://www.w3schools.com ' .
            $this->item->getSchemaUrl());

        $meta->appendChild($dom->createElement('standardversion', 'http://opendata.gosmonitor.ru/standard/3.0'));

        // passport details
        $meta->appendChild($dom->createElement('identifier', $this->item->getIdentifier()));

        $cdata = $dom->createCDATASection($this->item->getTitle());
        $elem = $dom->createElement('title');
        $elem->appendChild($cdata);

        $meta->appendChild($elem);

        $cdata = $dom->createCDATASection($this->item->getDescription());
        $elem = $dom->createElement('description');
        $elem->appendChild($cdata);
        $meta->appendChild($elem);
        unset($elem);
        unset($cdata);

        $meta->appendChild($dom->createElement('creator', $this->item->getOwner()));
        $meta->appendChild($dom->createElement('created', $this->item->getCreatedAt()->format("Ymd")));
        $meta->appendChild($dom->createElement('modified', $this->item->getUpdatedAt()->format("Ymd")));
        $meta->appendChild($dom->createElement('subject', $this->item->getSubject()));
        $meta->appendChild($dom->createElement('format', 'xml'));

        // data sets list
        $data = $dom->createElement('data');
        foreach ($this->item->getSets() as $set) {
            $dataversion = $dom->createElement('dataversion');

            $dataversion->appendChild($dom->createElement('source', $set->getUrl()));
            $dataversion->appendChild($dom->createElement('created', $set->getCreatedAt()->format("Ymd")));
            $dataversion->appendChild($dom->createElement('provenance', $set->getChanges()));
            $dataversion->appendChild($dom->createElement('valid', $set->getUpdatedAt()->format("Ymd")));
            $dataversion->appendChild($dom->createElement('structure', $set->getVersion()));

            $data->appendChild($dataversion);
        }
        $meta->appendChild($data);
        unset($data);

        // structure for data sets
        $structure = $dom->createElement('structure');
        foreach ($this->item->getSets() as $set) {
            $structureversion = $dom->createElement('structureversion');

            $structureversion->appendChild($dom->createElement('source', $set->getStructureUrl()));
            $structureversion->appendChild($dom->createElement('created', $set->getCreatedAt()->format("Ymd")));

            $structure->appendChild($structureversion);
        }
        $meta->appendChild($structure);
        unset($structure);

        // publisher
        $publisher = $dom->createElement('publisher');
        $publisher->appendChild($dom->createElement('name', $this->item->getPublisherName()));
        $publisher->appendChild($dom->createElement('phone', $this->item->getPublisherPhone()));
        $publisher->appendChild($dom->createElement('mbox', $this->item->getPublisherEmail()));
        $meta->appendChild($publisher);
        unset($publisher);


        $dom->appendChild($meta);

        return $dom->saveXML();
    }

    /**
     * @param OpendataPassportDTO $item
     */
    public function load(OpendataPassportDTO $item)
    {
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        return Response::FORMAT_XML;
    }
}