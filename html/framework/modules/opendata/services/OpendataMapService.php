<?php

namespace app\modules\opendata\services;

use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\models\OpendataSetProperty;
use app\modules\opendata\models\OpendataSetValue;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class OpendataMapService
 *
 * @package app\modules\opendata\services
 */
class OpendataMapService extends Component implements OpendataMapInterface
{
    /**
     * @var OpendataSet
     */
    public $model;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->model instanceof OpendataSet) {
            throw new InvalidConfigException(get_class($this->model) . ' is not an instance of ' . OpendataSet::class);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        /**
         * @see OpendataPassport
         */
        if ($this->model->hasMap()) {
            $indicators = $this->getIndicators();
            $region = $this->getRegion();

            if (!$indicators || !$region) {
                return null;
            }

            $values = OpendataSetValue::find()->where([
                'set_id' => $this->model->id,
            ])->all();

            if ($values) {
                $data = [];
                foreach ($values as $row) {
                    $item = [];
                    foreach ($indicators as $indicator) {
                        $item[] = [
                            'title' => $indicator['title'],
                            'value' => $row->{$indicator['prop']},
                        ];
                    }
                    $data[$row->{$region}] = $item;
                }

                return $data;
            }
        }

        return null;
    }

    /**
     * return array|null
     */
    protected function getIndicators()
    {
        $props = OpendataSetProperty::find()
            ->set($this->model->id)
            ->map(OpendataSetProperty::MAP_PROP_INDICATOR)
            ->all();

        if ($props) {
            $indicators = ArrayHelper::getColumn($props, function ($item) {
                return [
                    'prop' => $item->name,
                    'title' => $item->title,
                ];
            });

            return $indicators;
        }

        return null;
    }

    /**
     *
     * @return string|null
     */
    protected function getRegion()
    {
        $prop = OpendataSetProperty::find()
            ->set($this->model->id)
            ->map(OpendataSetProperty::MAP_PROP_REGION)
            ->one();

        return $prop ? $prop->name : null;
    }
}
