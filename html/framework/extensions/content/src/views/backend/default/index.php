<?php

use krok\content\models\Content;
use krok\grid\ActiveColumn;
use krok\grid\DatePickerColumn;
use krok\grid\HiddenColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel krok\content\models\ContentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Content');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">

    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>

    <div class="card-header">
        <p>
            <?= Html::a(Yii::t('system', 'Create'), ['create'], [
                'class' => 'btn btn-success',
            ]) ?>
        </p>
    </div>

    <div class="card-content">

        <?= GridView::widget([
            'tableOptions' => ['class' => 'table'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\ActionColumn'],
                ['class' => 'yii\grid\SerialColumn'],
                'alias',
                [
                    'class' => ActiveColumn::class,
                    'attribute' => 'title',
                ],
                [
                    'attribute' => 'layout',
                    'filter' => $searchModel::getLayouts(),
                    'value' => function (Content $model) {
                        return $model->getLayout();
                    },
                ],
                [
                    'attribute' => 'view',
                    'filter' => $searchModel::getViews(),
                    'value' => function (Content $model) {
                        return $model->getView();
                    },
                ],
                [
                    'class' => HiddenColumn::class,
                    'attribute' => 'hidden',
                ],
                [
                    'class' => DatePickerColumn::class,
                    'attribute' => 'createdAt',
                ],
                [
                    'class' => DatePickerColumn::class,
                    'attribute' => 'updatedAt',
                ],
            ],
        ]); ?>

    </div>
</div>
