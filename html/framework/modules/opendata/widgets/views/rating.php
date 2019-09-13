<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 09.08.17
 * Time: 12:17
 */

use app\modules\opendata\models\OpendataRating;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model \app\modules\opendata\models\OpendataRating */
?>
<div class="border-block block-arrow">
    <h3 class="text-black text-center">Оцените набор данных</h3>
    <?php $form = ActiveForm::begin([
        'action' => ['/opendata/default/rate', 'id' => $model->passport_id],
        'fieldConfig' => [
            'template' => '{input}',
        ],
        'options' => [
            'class' => 'rate-form',
            'id' => 'opendataRateForm',
        ],
        'enableClientScript' => false,
    ]); ?>
    <?= $form->field($model, 'passport_id')->hiddenInput(); ?>
    <div class="rate_row"
         data-rate="<?= round($model->rating); ?>"
         data-name="<?= $model->formName() . '[rate]'; ?>"
         data-max="<?= OpendataRating::MAX_RATE; ?>"></div>
    <button class="btn-rate btn btn-block btn-primary">Голосовать</button>
    <?php ActiveForm::end(); ?>
</div>
