<?php

use zima\charts\models\Chart;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\opendata\models\OpendataPassport */
?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'code')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>

<?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

<?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'owner')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'publisher_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'publisher_email')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'publisher_phone')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'update_frequency')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'import_data_url')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'import_schema_url')->textInput(['maxlength' => true]) ?>

<?//= $form->field($model, 'chart_type')->dropDownList(Chart::getTypeList(), ['prompt' => 'Ничего не выбрано']) ?>

<?= $form->field($model, 'table_view')->dropDownList($model::getYesNoList()) ?>

<?= $form->field($model, 'hidden')->dropDownList($model::getHiddenList()) ?>

<?= $form->field($model, 'archive')->dropDownList($model::getArchiveList()) ?>
