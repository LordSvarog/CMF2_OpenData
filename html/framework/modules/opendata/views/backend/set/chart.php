<?php

use zima\charts\widgets\ChartJsWidget;

/* @var $this yii\web\View */
/* @var $chartConfig array */
?>
<?= ChartJsWidget::widget([
    'config' => $chartConfig,
]);
?>
