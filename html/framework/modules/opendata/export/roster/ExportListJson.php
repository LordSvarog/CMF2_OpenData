<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\roster;

use app\modules\opendata\dto\OpendataListDTO;
use yii\helpers\Json;
use yii\web\Response;

class ExportListJson implements ExportListInterface
{

    /**
     * @var \stdClass[]
     */
    protected $list = [];

    /**
     * @return string
     */
    public function render(): string
    {
        return Json::encode([
            'standardversion' => 'http://opendata.gosmonitor.ru/standard/3.0',
            'meta' => $this->list,
        ]);
    }

    /**
     * @param OpendataListDTO $item
     */
    public function addItem(OpendataListDTO $item)
    {
        array_push($this->list, $item->toStdObject());
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        return Response::FORMAT_JSON;
    }

}