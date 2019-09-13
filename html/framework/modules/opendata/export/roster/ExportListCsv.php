<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\roster;

use app\modules\opendata\dto\OpendataListDTO;
use Yii;
use yii\web\Response;

class ExportListCsv implements ExportListInterface
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
        $str = '"property","title","value","format"' . "\r\n" .
            '"standard version","Версия методическихрекомендаций","http://opendata.gosmonitor.ru/standard/3.0",""' . "\r\n";
        foreach ($this->list as $item) {
            $str .= $this->escape($item->getIdentifier()) . ',' .
                $this->escape($item->getTitle()) . ',' .
                $this->escape($item->getUrl()) . ',' .
                $this->escape($item->getFormat()) .
                "\r\n";
        }

        return $str;
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
        Yii::$app->response->getHeaders()->set('Content-Type', 'text/csv; charset=UTF-8');

        return Response::FORMAT_RAW;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function escape(string $str): string
    {
        return '"' . addslashes($str) . '"';
    }
}