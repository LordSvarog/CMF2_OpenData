<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\roster;

use app\modules\opendata\dto\OpendataListDTO;
use yii\web\Response;

class ExportListXml implements ExportListInterface
{

    /**
     * @var OpendataListDTO[]
     */
    protected $list = [];

    /**
     * @return string
     */
    public function render(): string
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $list = $dom->createElement('list');
        $list->appendChild($dom->createElement('standardversion', 'http://opendata.gosmonitor.ru/standard/3.0'));
        $meta = $dom->createElement('meta');

        foreach ($this->list as $item) {
            $itemNode = $dom->createElement('item');
            $itemNode->appendChild($dom->createElement('identifier', $item->getIdentifier()));
            $cdata = $dom->createCDATASection($item->getTitle());
            $elem = $dom->createElement('title');
            $elem->appendChild($cdata);
            $itemNode->appendChild($elem);

            $itemNode->appendChild($dom->createElement('link', $item->getUrl()));
            $itemNode->appendChild($dom->createElement('format', $item->getFormat()));
            $meta->appendChild($itemNode);
        }
        $list->appendChild($meta);
        $dom->appendChild($list);

        return $dom->saveXML();
    }

    /**
     * @param OpendataListDTO $item
     */
    public function addItem(OpendataListDTO $item)
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