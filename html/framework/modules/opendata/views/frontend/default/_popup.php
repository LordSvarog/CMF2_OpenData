<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 03.08.17
 * Time: 15:21
 */

use yii\captcha\Captcha;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $formModel \app\modules\opendata\forms\CommentForm */
/** @var $model \app\modules\opendata\models\OpendataPassport */
?>

<?php $this->beginBlock('modalFeedback'); ?>
<div id="modalFeedback" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Напишите нам</h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'action' => (isset($model) ? ['send-comment', 'id' => $model->id] : ['send-comment']),
                    'options' => [
                        'class' => 'form form-comment',
                        'id' => 'opendataCommentForm',
                    ],
                    'enableClientScript' => false,
                ]); ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="error-message"></div>
                        <div class="pd-bottom-10">Оставьте комментарии, поправки и предложения к набору</div>
                        <?= $form->field($formModel, 'name', [
                            'template' => '{label}' . PHP_EOL . '{input}' . PHP_EOL . '{error}',
                            'options' => [
                                'class' => 'form-group form-group--placeholder-fix',
                            ],
                            'labelOptions' => [
                                'class' => 'placeholder',
                            ],
                        ])->textInput(); ?>

                        <?= $form->field($formModel, 'email', [
                            'template' => '{label}' . PHP_EOL . '{input}' . PHP_EOL . '{error}',
                            'options' => [
                                'class' => 'form-group form-group--placeholder-fix',
                            ],
                            'labelOptions' => [
                                'class' => 'placeholder',
                            ],
                        ])->textInput(); ?>

                        <?= $form->field($formModel, 'comment', [
                            'template' => '{label}' . PHP_EOL . '{input}' . PHP_EOL . '{error}',
                            'options' => [
                                'class' => 'form-group form-group--placeholder-fix',
                            ],
                            'labelOptions' => [
                                'class' => 'placeholder',
                            ],
                        ])->textarea(); ?>

                    </div>
                    <?= $form->field($formModel, 'verifyCode')->widget(
                        Captcha::className(), [
                        'captchaAction' => '/opendata/default/captcha',
                        'template' => '<div class="col-lg-6 captcha-image">{image}</div>' .
                            '<div class="col-lg-6">' .
                            '<div class="form-group form-group--placeholder-fix">' .
                            '<label for="captcha" class="placeholder">Введите код с картинки </label>' .
                            '{input}' .
                            '</div>' .
                            '</div>',
                    ])->label(false) ?>
                    <div class="col-lg-12">
                        <div class="form-group clearfix pd-top-20 pd-bottom-20">
                            <label class="wrap-check">
                                <input type="checkbox" name="<?= $formModel->formName() . '[deal]'; ?>" value="1"
                                       id="inputDeal"/>
                                <span class="placeholder text-black">Разрешаю обработку персональных данных</span>
                            </label>
                        </div>
                        <div class="two-btn">
                            <button id="recourseFormBtnSubmit" disabled="" type="submit"
                                    class="btn btn-primary btn-lg two-btn__elem">Отправить
                            </button>
                        </div>
                        <div class="text-black-b pd-top-20">Поля, отмеченные *, обязательны для заполнения</div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('modalOk'); ?>
<div id="modalOk" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Спасибо!</h4>
                <div class="text-black pd-top-10">Ваше сообщение отправлено.</div>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>
