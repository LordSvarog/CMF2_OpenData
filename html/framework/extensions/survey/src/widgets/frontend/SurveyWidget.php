<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 26.11.18
 * Time: 13:28
 */

namespace krok\survey\widgets\frontend;

use krok\survey\models\Survey;
use yii\base\Widget;

/**
 * Class SurveyWidget
 *
 * @package krok\survey\widgets\frontend
 */
class SurveyWidget extends Widget
{
    /**
     * @var string
     */
    public $view = 'survey';

    /**
     * @var Survey
     */
    public $model;

    /**
     * @return string
     */
    public function run()
    {
        return $this->render($this->view, [
            'model' => $this->model,
        ]);
    }
}
