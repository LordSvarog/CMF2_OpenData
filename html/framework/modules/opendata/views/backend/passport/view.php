<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\opendata\models\OpendataPassport */
/* @var $setsDataProvider \yii\data\ActiveDataProvider */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Opendata'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">

    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>

    <div class="card-header">
        <p>
            <?= Html::a(Yii::t('system', 'Update'), ['update', 'id' => $model->id], [
                'class' => 'btn btn-primary',
            ]) ?>
            <?= Html::a(Yii::t('system', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('system', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <div class="card-content">

        <?= DetailView::widget([
            'options' => ['class' => 'table'],
            'model' => $model,
            'attributes' => [
                'id',
                'title',
                'code',
                'description:html',
                'subject',
                'owner',
                'publisher_name',
                'publisher_email:email',
                'publisher_phone',
                'update_frequency',
                'import_data_url:url',
                'import_schema_url:url',
                [
                    'attribute' => 'chart_type',
                    'value' => $model->getChartTypeAsString(),
                ],
                'table_view:boolean',
                [
                    'attribute' => 'hidden',
                    'value' => $model->getHidden(),
                ],
                'created_at',
                'updated_at',
            ],
        ]) ?>

    </div>

    <div class="card-header">
        <h4 class="card-title">Наборы открытых данных</h4>
        <p>
            <?= Html::a(Yii::t('system', 'Create'), ['/opendata/set/create', 'id' => $model->id], [
                'class' => 'btn btn-success',
            ]) ?>
        </p>
    </div>

    <div class="card-content">

        <?= GridView::widget([
            'tableOptions' => ['class' => 'table'],
            'dataProvider' => $setsDataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'title',
                'version',
                'changes',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'urlCreator' => function (
                        string $action,
                        \app\modules\opendata\models\OpendataSet $model
                    ) {
                        switch ($action) {
                            case 'update':
                                return Url::to(['/opendata/set/update', 'id' => $model->id]);
                                break;
                            case 'delete':
                                return Url::to(['/opendata/set/delete', 'id' => $model->id]);
                                break;
                        }

                        return false;
                    },
                ],
            ],
        ]); ?>
    </div>
</div>
