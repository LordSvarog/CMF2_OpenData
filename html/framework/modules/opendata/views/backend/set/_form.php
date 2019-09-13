<?php

use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\opendata\models\OpendataSet */
?>

<?= $form->field($model, 'passport_id')->hiddenInput()->label(false); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'version')->textInput(['maxlength' => true, 'readonly' => true]) ?>

<?= $form->field($model, 'changes')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'map_region')->dropDownList($model::getYesNoList()) ?>

<?= $form->field($model, 'hidden')->dropDownList($model::getHiddenList()) ?>

<?= $form->field($model, 'created_at')->widget(DatePicker::className(), [
    'dateFormat' => 'php:Y-m-d',
    'options' => [
        'class' => 'form-control',
    ],
]) ?>

<?= $form->field($model, 'updated_at')->widget(DatePicker::className(), [
    'dateFormat' => 'php:Y-m-d',
    'options' => [
        'class' => 'form-control',
    ],
]) ?>
