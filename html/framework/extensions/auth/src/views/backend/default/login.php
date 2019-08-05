<?php

use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model krok\auth\models\Login */
/* @var $form ActiveForm */

/** @var \krok\configure\helpers\ConfigureHelperInterface $configure */
$configure = Yii::createObject(\krok\configure\helpers\ConfigureHelperInterface::class);

$this->title = 'Авторизация';
?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-10 col-md-offset-3 col-sm-offset-1">
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => '{label}' . PHP_EOL . '{input}' . PHP_EOL . '{error}',
                ],
            ]); ?>
            <div class="card card-login swal2-show" data-background="color" data-color="blue">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-login__title">
                                <a href="<?= Url::to() ?>" class="logo-big">
                                    <?= Html::img($configure->get(\krok\paperdashboard\Configurable::class,
                                        'logoLogin'),
                                        ['alt' => 'Лого']) ?>
                                </a>
                                <span class="name">
                                    <?= $configure->get(\krok\paperdashboard\Configurable::class, 'title') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-login__body row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <?= $form->field($model, 'login')->textInput(['autofocus' => 'autofocus']) ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <?= $form->field($model, 'password')->passwordInput() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($configure->get(\krok\auth\Configurable::class, 'useCaptcha')) : ?>
                    <div class="container-fluid gray-bg">
                        <div class="card-login__vertify row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <?= $form->field($model, 'verifyCode')->widget(
                                        Captcha::class, [
                                        'captchaAction' => '/auth/default/captcha',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="container-fluid">
                    <div class="card-login__footer row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <?= Html::submitButton('Авторизация', ['class' => 'btn btn-fill btn-wd']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
