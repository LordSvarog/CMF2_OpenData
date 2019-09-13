<?php

use app\modules\opendata\assets\OpendataBackAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\opendata\models\OpendataSet */

$this->title = 'Импорт данных';
$this->params['breadcrumbs'][] = [
    'label' => 'Паспорт открытых данных',
    'url' => ['/opendata/passport/view', 'id' => $model->passport_id],
];
$this->params['breadcrumbs'][] = [
    'label' => $model->title,
    'url' => ['update', 'id' => $model->id],
];
$this->params['breadcrumbs'][] = $this->title;

OpendataBackAsset::register($this);
?>
<div class="card">

    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>

    <div class="card-content">

        <?php $form = ActiveForm::begin(); ?>

        <div class="form-group">
            <label>Разделитель</label>
            <?= Html::dropDownList('delimiter', null, $model::getDelimiterList(), ['class' => 'form-control']); ?>
        </div>

        <div class="form-group">
            <label>Данные</label>
            <?= Html::textarea('import', '', ['class' => 'form-control', 'rows' => 20]); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Импортировать данные',
                ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('system', 'Back'),
                Url::to(['/opendata/set/update', 'id' => $model->id]),
                [
                    'class' => 'btn btn-primary',
                ]
            ); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
