<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 9:56
 */

namespace app\modules\opendata\dto;

use yii\helpers\ArrayHelper;

/**
 * Class OpendataDataImportDTO
 *
 * @package app\modules\opendata\dto
 */
class OpendataDataDTO
{

    /**
     * @var array
     */
    protected $list;

    /**
     * @param $values
     */
    public function setBatchPropertiesValues($values)
    {
        $this->list = $values;
    }

    /**
     * @param $property string
     * @param $value string
     */
    public function setPropertyValue($property, $value)
    {
        $this->list[$property] = $value;
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    public function getPropertyValue($property)
    {
        return ArrayHelper::getValue($this->list, $property, '');
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return array_keys($this->list);
    }

    /**
     * @return array
     */
    public function getValueAsArray()
    {
        return $this->list;
    }

}