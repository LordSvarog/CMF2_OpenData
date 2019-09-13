<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\opendata\models\OpendataSet */

$this->title = Yii::t('system', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Opendata'), 'url' => ['/opendata/passport/index']];
$this->params['breadcrumbs'][] = [
    'label' => 'Паспорт открытых данных',
    'url' => ['/opendata/passport/view', 'id' => $model->passport_id],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">

    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>

    <div class="card-content">

        <?php $form = ActiveForm::begin(['id' => 'form-set']); ?>

        <?= $this->render('_form', ['form' => $form, 'model' => $model]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('system', 'Create'),
                ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('system', 'Back'),
                Url::to(['/opendata/passport/view', 'id' => $model->passport_id]),
                [
                    'class' => 'btn btn-primary',
                ]
            ); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
