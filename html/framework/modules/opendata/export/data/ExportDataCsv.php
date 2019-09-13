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
use Yii;
use yii\base\Exception;
use yii\web\Response;

class ExportDataCsv implements ExportDataInterface
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

        $str = '';
        if (!empty($this->list)) {
            $firstDto = current($this->list);
            foreach ($firstDto->getProperties() as $property) {
                $str .= ($str > '' ? ',' : '') . $this->escape($property);
            }
            $str .= "\r\n";
            foreach ($this->list as $item) {
                $row = '';
                foreach ($this->schema->getProperties() as $propertyDTO) {
                    $value = $item->getPropertyValue($propertyDTO->getName());
                    $row .= ($row > '' ? ',' : '') . $this->escape($value);
                }
                $str .= $row . "\r\n";
            }
        }

        return $str;
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