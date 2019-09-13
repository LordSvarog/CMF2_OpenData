<?php

use zima\charts\widgets\ChartJsWidget;

/* @var $this yii\web\View */
/* @var $model \app\modules\opendata\models\OpendataSet */
/* @var $chartConfig array */

$this->title = $model->passport->title;

$this->params['breadcrumbs'] = [
    ['label' => 'Открытые данные', 'url' => ['/opendata']],
    ['label' => $this->title],
];

$this->params['share-page'] = true;
?>

<div class="clearfix">
    <div class="col-md-12 pd-bottom-60" style="width: 100%">
        <h1 class="page-title text-black"><?= $this->title ?></h1>
        <div class="pd-bottom-70 pd-top-30">

            <?= ChartJsWidget::widget([
                'config' => $chartConfig,
            ]) ?>

        </div>
    </div>
</div>
