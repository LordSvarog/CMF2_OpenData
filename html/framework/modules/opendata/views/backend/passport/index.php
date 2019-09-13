<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\opendata\models\OpendataPassportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Opendata');
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
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'title',
                'code',
                //'description:ntext',
                'subject',
                // 'owner',
                // 'publisher_name',
                // 'publisher_email:email',
                // 'publisher_phone',
                // 'update_frequency',
                // 'import_data_url:url',
                // 'import_schema_url:url',
                [
                    'attribute' => 'hidden',
                    'filter' => $searchModel::getHiddenList(),
                    'value' => function (\app\modules\opendata\models\OpendataPassport $model) {
                        return $model->getHidden();
                    },
                ],
                // 'created_at',
                // 'updated_at',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>

    </div>
</div>
