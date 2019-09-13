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
use Yii;
use yii\web\Response;

class ExportDataSchemaCsv implements ExportDataSchemaInterface
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
        $str = $this->escape('field name') . ',' .
            $this->escape('english description') . ',' .
            $this->escape('russian description') . ',' .
            $this->escape('format') . "\r\n";

        foreach ($this->properties as $property) {
            $str .= $this->escape($property->getName()) . ',' .
                $this->escape($property->getName()) . ',' .
                $this->escape($property->getTitle()) . ',' .
                $this->escape($property->getFormat()) . ',' .
                "\r\n";
        }

        return $str;
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