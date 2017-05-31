<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 10.02.17
 * Time: 11:31
 */

namespace app\modules\cp\components\frontend;

/**
 * Class Controller
 *
 * @package app\modules\cp\components\frontend
 */
class Controller extends \yii\web\Controller
{
    /**
     * @var string
     */
    public $layout = '//index';

    public function init()
    {
        parent::init();

        $this->registerViewPath();
    }

    protected function registerViewPath()
    {
        if ($this->viewPath === null) {
            $path = trim($this->module->id . '/' . $this->id, '/');
            $this->setViewPath('@app/views/modules/' . $path);
        }
    }
}