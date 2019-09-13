<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\passport;

use app\modules\opendata\dto\OpendataPassportDTO;
use yii\helpers\Json;
use yii\web\Response;

class ExportPassportJson implements ExportPassportInterface
{

    /**
     * @var OpendataPassportDTO
     */
    protected $item;

    /**
     * @return string
     */
    public function render(): string
    {
        $publisher = new \stdClass();
        $publisher->name = $this->item->getPublisherName();
        $publisher->phone = $this->item->getPublisherPhone();
        $publisher->mbox = $this->item->getPublisherEmail();
        $ret = [
            'standardversion' => 'http://opendata.gosmonitor.ru/standard/3.0',
            'identifier' => $this->item->getIdentifier(),
            'title' => $this->item->getTitle(),
            'description' => $this->item->getDescription(),
            'creator' => $this->item->getOwner(),
            'created' => $this->item->getCreatedAt()->format('Ymd'),
            'modified' => $this->item->getUpdatedAt()->format('Ymd'),
            'subject' => $this->item->getSubject(),
            'format' => 'json',
            'data' => [],
            'structure' => [],
            'publisher' => $publisher,
        ];

        foreach ($this->item->getSets() as $set) {
            array_push($ret['data'], $set->toStdObject());
            array_push($ret['structure'], $set->toStructureStdObject());
        }

        return Json::encode($ret);
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
        return Response::FORMAT_JSON;
    }

}