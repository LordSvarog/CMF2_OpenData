<?php

use krok\cabinet\models\Log;
use krok\extend\grid\ActiveColumn;
use krok\extend\grid\DatePickerColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $clients [] */
/* @var $searchModel \krok\cabinet\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Log');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">

    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>

    <div class="card-content">

        <?= GridView::widget([
            'tableOptions' => ['class' => 'table'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                [
                    'class' => ActiveColumn::class,
                    'attribute' => 'clientId',
                    'filter' => $clients,
                    'value' => function (Log $model) {
                        return ArrayHelper::getValue($model->clientRelation, 'login');
                    },
                    'action' => '/cabinet/client/view',
                ],
                [
                    'attribute' => 'status',
                    'filter' => $searchModel::getStatusList(),
                    'value' => function (Log $model) {
                        return $model->getStatus();
                    },
                ],
                [
                    'attribute' => 'ip',
                    'value' => function (Log $model) {
                        return long2ip($model->ip);
                    },
                ],
                [
                    'class' => DatePickerColumn::class,
                    'attributeFilter' => 'createdAtFrom',
                    'attribute' => 'createdAt',
                ],
                [
                    'class' => DatePickerColumn::class,
                    'attributeFilter' => 'createdAtTo',
                    'attribute' => 'createdAt',
                ],
            ],
        ]); ?>

    </div>
</div>
