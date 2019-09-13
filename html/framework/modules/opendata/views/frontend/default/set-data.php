<?php

use app\modules\opendata\models\OpendataSet;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model OpendataSet */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $properties \app\modules\opendata\models\OpendataSetProperty[] */

$this->title = $model->passport->title;

$this->params['breadcrumbs'] = [
    ['label' => 'Открытые данные', 'url' => ['/opendata']],
    ['label' => $this->title],
];

$this->params['share-page'] = true;

$columns = [
];
foreach ($properties as $property) {
    array_push($columns, [
        'label' => $property->title,
        'attribute' => $property->name,
    ]);
}
?>

<div class="clearfix">
    <div class="main main-full pd-bottom-60">
        <div class="main">
            <h1 class="page-title text-black"><?= $this->title ?></h1>
            <p class="page-date text-light"><?= Yii::$app->formatter->asDate($model->created_at) ?></p>
        </div>
        <div class="main-aside">
            <div class="border-block block-arrow border-block--date">
                <p class="text-light">Дата обновления:</p>
                <span><?= Yii::$app->formatter->asDate($model->updated_at) ?></span>
            </div>
        </div>
        <div class="row pd-bottom-70 pd-top-0">
            <div class="col-xs-12">
                <h3 class="table-caption"><?= $this->title ?></h3>
                <div class="wrap-table">
                    <?= GridView::widget([
                        'tableOptions' => ['class' => 'table-light-style text-black'],
                        'dataProvider' => $dataProvider,
                        'layout' => '{items}',
                        'columns' => $columns,
                    ]); ?>
                </div>
                <div class="wrap-pagination wrap-pagination--page-amount">
                    <?= $this->render('//parts/pagination-with-perpage', [
                        'pagination' => $dataProvider->getPagination(),
                        'perPage' => OpendataSet::getPerPageList(),
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
