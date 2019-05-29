<?php

use krok\cabinet\interfaces\LoginAsInterface;
use krok\extend\grid\BlockedColumn;
use krok\extend\grid\DatePickerColumn;
use yii\bootstrap\Html as BootstrapHtml;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel krok\cabinet\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Client');
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
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete} {login-as}',
                    'buttons' => [
                        'login-as' => function ($url, $model) {
                            if ($model instanceof LoginAsInterface) {
                                return Html::a(BootstrapHtml::icon('log-in'),
                                    ['login-as', 'id' => $model->id], [
                                        'title' => 'Войти как',
                                    ]
                                );
                            } else {
                                return false;
                            }
                        },
                    ],
                ],
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                'login',
                [
                    'class' => BlockedColumn::class,
                    'attribute' => 'blocked',
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
