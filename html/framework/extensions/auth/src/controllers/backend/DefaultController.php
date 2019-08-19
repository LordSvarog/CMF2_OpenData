<?php

namespace krok\auth\controllers\backend;

use krok\auth\Configurable;
use krok\auth\models\Login;
use krok\configure\ConfigureInterface;
use krok\system\components\backend\Controller;
use Yii;
use yii\base\Module;
use yii\captcha\CaptchaAction;

/**
 * Class DefaultController
 *
 * @package krok\auth\controllers\backend
 */
class DefaultController extends Controller
{
    /**
     * @var string
     */
    public $layout = '@krok/system/views/backend/layouts/login.php';

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * DefaultController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param ConfigureInterface $configure
     * @param array $config
     */
    public function __construct(string $id, Module $module, ConfigureInterface $configure, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->configurable = $configure->get(Configurable::class);
    }

    /**
     * @return array
     */
    public function actions()
    {
        if ($this->configurable->useCaptcha) {
            return [
                'captcha' => [
                    'class' => CaptchaAction::class,
                    'fixedVerifyCode' => YII_ENV_TEST ? 'cmf2' : null,
                ],
            ];
        } else {
            return parent::actions();
        }
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->redirect(Yii::$app->getUser()->getReturnUrl());
        }

        $model = new Login();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->redirect(Yii::$app->getUser()->getReturnUrl());
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout(false);

        return $this->goHome();
    }
}
