<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\passport;

use app\modules\opendata\dto\OpendataPassportDTO;
use Yii;
use yii\web\Response;

class ExportPassportCsv implements ExportPassportInterface
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
        $str = '"property","value"' . "\r\n" .
            '"standard version","http://opendata.gosmonitor.ru/standard/3.0"' . "\r\n";


        $sets = $this->item->getSets();
        $lastSet = end($sets);

        $str .= $this->escape('identifier') . ',' . $this->escape($this->item->getIdentifier()) . "\r\n";
        $str .= $this->escape('title') . ',' . $this->escape($this->item->getTitle()) . "\r\n";
        $str .= $this->escape('description') . ',' . $this->escape($this->item->getDescription()) . "\r\n";
        $str .= $this->escape('creator') . ',' . $this->escape($this->item->getOwner()) . "\r\n";
        $str .= $this->escape('created') . ',' . $this->escape($this->item->getCreatedAt()->format('Ymd')) . "\r\n";
        $str .= $this->escape('modified') . ',' . $this->escape($this->item->getUpdatedAt()->format('Ymd')) . "\r\n";
        $str .= $this->escape('subject') . ',' . $this->escape($this->item->getSubject()) . "\r\n";
        $str .= $this->escape('format') . ',' . $this->escape('csv') . "\r\n";
        // last set changes
        $str .= $this->escape('provenance') . ',' . $this->escape($lastSet->getChanges()) . "\r\n";
        $str .= $this->escape('valid') . ',' . $this->escape($lastSet->getCreatedAt()->format('Ymd')) . "\r\n";
        // publisher
        $str .= $this->escape('publishername') . ',' . $this->escape($this->item->getPublisherName()) . "\r\n";
        $str .= $this->escape('publisherphone') . ',' . $this->escape($this->item->getPublisherPhone()) . "\r\n";
        $str .= $this->escape('publishermbox') . ',' . $this->escape($this->item->getPublisherEmail()) . "\r\n";

        foreach ($this->item->getSets() as $set) {
            $str .= $this->escape($set->getDataCode()) . ',' . $this->escape($set->getUrl()) . "\r\n";
            $str .= $this->escape($set->getSchemaCode()) . ',' . $this->escape($set->getStructureUrl()) . "\r\n";
        }

        return $str;
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