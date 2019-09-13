<?php

use app\modules\opendata\assets\OpendataBackAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\opendata\models\OpendataSet */
/* @var $properties app\modules\opendata\models\OpendataSetProperty[] */

$this->title = Yii::t('system', 'Update') . ' : ' . $model->title;
$this->params['breadcrumbs'][] = [
    'label' => 'Паспорт открытых данных',
    'url' => ['/opendata/passport/view', 'id' => $model->passport_id],
];
$this->params['breadcrumbs'][] = Yii::t('system', 'Update');

OpendataBackAsset::register($this);
?>
<div class="card">

    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>

    <div class="card-header">
        <p>
            <?= Html::a('Импортировать данные', ['import', 'id' => $model->id], [
                'class' => 'btn btn-primary',
            ]) ?>
            <?= Html::a('Просмотр данных', ['data', 'id' => $model->id], [
                'class' => 'btn btn-default',
            ]) ?>
            <?php if ($model->hasChart()): ?>
                <?= Html::a('Диаграмма', ['chart', 'id' => $model->id], [
                    'class' => 'btn btn-default',
                ]) ?>
            <?php endif; ?>
            <?= Html::a('Удалить данные набора', ['delete-data', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Удалить данные набора?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <div class="card-content">
        <?php $form = ActiveForm::begin(['id' => 'form-set']); ?>

        <?= $this->render('_form', ['form' => $form, 'model' => $model]) ?>

        <?= $this->render('_properties', ['form' => $form, 'model' => $model, 'properties' => $properties]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('system', 'Update'),
                ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('system', 'Back'),
                Url::to(['/opendata/passport/view', 'id' => $model->passport_id]),
                [
                    'class' => 'btn btn-primary',
                ]
            ); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
