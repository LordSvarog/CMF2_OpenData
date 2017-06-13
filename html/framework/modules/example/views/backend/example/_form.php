<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\example\models\Example */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card-content">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'hidden')->dropDownList($model::getHiddenList()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('cp', 'Create') : Yii::t('cp', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>