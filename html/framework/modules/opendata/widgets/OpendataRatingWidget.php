<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 09.08.17
 * Time: 11:33
 */

namespace app\modules\opendata\widgets;

use app\modules\opendata\models\OpendataRating;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Json;

class OpendataRatingWidget extends Widget
{
    /**
     * @var int
     */
    public $passport_id;

    /**
     * @var OpendataRating
     */
    protected $model;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->passport_id) {
            throw new InvalidConfigException('Option "passport_id" must be set');
        }

        $this->model = OpendataRating::findOne(['passport_id' => $this->passport_id]);

        if (!$this->model) {
            $this->model = new OpendataRating([
                'scenario' => OpendataRating::SCENARIO_CREATE,
                'passport_id' => $this->passport_id,
                'count' => 0,
                'rating' => 0,
            ]);
        } else {
            $this->model->setScenario(OpendataRating::SCENARIO_UPDATE);
        }
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->registerAssets();

        $rates = [];
        if (Yii::$app->request->cookies->has('opendata_rating')) {
            $rates = Json::decode(Yii::$app->request->cookies->get('opendata_rating'));
        }
        if ($rates && isset($rates[$this->passport_id])) {
            $this->model->previousRate = $rates[$this->passport_id];
        }

        return $this->render('rating', ['model' => $this->model]);
    }

    /**
     * @return void
     */
    protected function registerAssets()
    {
        OpendataRatingWidgetAsset::register($this->getView());

    }
}