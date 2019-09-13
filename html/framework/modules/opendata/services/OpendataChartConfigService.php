<?php

namespace app\modules\opendata\services;

use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\models\OpendataSetProperty;
use app\modules\opendata\models\OpendataSetValue;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use zima\charts\models\Chart;
use zima\charts\models\ChartOptions;
use zima\charts\services\ChartConfigService;

/**
 * Class OpendataChartConfigService
 *
 * @package app\modules\opendata\services
 */
class OpendataChartConfigService extends Component implements OpendataChartConfigInterface
{
    /**
     * @var OpendataSet
     */
    public $model;

    /**
     * @var bool
     */
    public $noTitle = false;

    /**
     * @var OpendataSetValue[]
     */
    private $values;

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
     * @return array|null
     */
    public function getConfig()
    {
        /**
         * @see OpendataPassport
         */
        if ($this->model->passport->chart_type) {
            $this->values = OpendataSetValue::find()->where([
                'set_id' => $this->model->id,
            ])->all();

            if ($this->values) {
                $categories = $this->getCategories();
                $series = $this->getSeries();
                $data = $categories ? $this->getData($categories) : [];

                $options = new ChartOptions();

                $chart = new Chart([
                    'title' => $this->noTitle ? '' : $this->model->passport->title,
                    'type' => $this->model->passport->chart_type,
                    'options' => $options,
                    'categories' => $categories,
                    'series' => $series['data'],
                    'data' => $data,
                ]);

                $service = new ChartConfigService();
                $config = $service->getConfig($chart);

                return $config;
            }
        }

        return null;
    }

    /**
     * return array|null
     */
    protected function getCategories()
    {
        $category = OpendataSetProperty::find()
            ->set($this->model->id)
            ->chart(OpendataSetProperty::CHART_PROP_CATEGORY)
            ->all();

        if ($category) {
            $categories = ArrayHelper::getColumn($category, function ($item) {
                return [
                    'prop' => $item->name,
                    'name' => $item->title,
                    'color' => '',
                ];
            });

            return $categories;
        }

        return null;
    }

    /**
     * @return array|null
     */
    protected function getSeries()
    {
        $seriesProp = OpendataSetProperty::find()
            ->set($this->model->id)
            ->chart(OpendataSetProperty::CHART_PROP_SERIES)
            ->one();


        if ($seriesProp) {
            return [
                'label' => $seriesProp->title,
                'data' => ArrayHelper::getColumn($this->values, $seriesProp->name),
            ];
        }

        return null;
    }

    /**
     * @param $categories
     *
     * @return array
     */
    protected function getData($categories)
    {
        $data = [];

        $categoryProp = ArrayHelper::getColumn($categories, 'prop');

        foreach ($this->values as $row) {
            $serie = [];
            foreach ($categoryProp as $prop) {
                $serie[] = str_replace(',', '.', $row->{$prop});
            }
            $data[] = $serie;
        }

        return $data;
    }
}
