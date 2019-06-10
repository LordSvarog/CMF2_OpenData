<?php

use krok\passwordEye\PasswordEyeWidget;
use krok\select2\Select2Widget;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model krok\auth\models\Auth */
/* @var $roles [] */
?>

<?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'password')->widget(
    PasswordEyeWidget::class
) ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'blocked')->widget(Select2Widget::class, [
    'items' => $model::getBlockedList(),
]) ?>

<?= $form->field($model, 'roles')->widget(Select2Widget::class, [
    'items' => $roles,
    'options' => [
        'multiple' => true,
    ],
]) ?>
