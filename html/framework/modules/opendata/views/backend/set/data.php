<?php

use app\modules\opendata\assets\OpendataBackAsset;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\opendata\models\OpendataSet */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $properties \app\modules\opendata\models\OpendataSetProperty[] */

$this->title = 'Просмотр данных';
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

$columns = [
    ['class' => 'yii\grid\SerialColumn'],
];
foreach ($properties as $property) {
    array_push($columns, [
        'label' => $property->title,
        'attribute' => $property->name,
    ]);
}

?>
<div class="card">

    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>

    <div class="card-content">
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table'],
            'dataProvider' => $dataProvider,
            'columns' => $columns,
        ]); ?>

        <div class="form-group">
            <?= Html::a(Yii::t('system', 'Back'),
                Url::to(['/opendata/set/update', 'id' => $model->id]),
                [
                    'class' => 'btn btn-primary',
                ]
            ); ?>
        </div>

    </div>

</div>
