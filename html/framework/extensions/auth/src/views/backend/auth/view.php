<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model krok\auth\models\Auth */
/* @var $roles [] */

$this->title = $model->login;
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Auth'), 'url' => ['index']];
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
            <?= Html::a('Сбросить токены', ['refresh-token', 'id' => $model->id], [
                'class' => 'btn btn-warning',
            ]) ?>
        </p>
    </div>

    <div class="card-content">

        <?= DetailView::widget([
            'options' => ['class' => 'table'],
            'model' => $model,
            'attributes' => [
                'id',
                'login',
                'authKey',
                'accessToken',
                'email:email',
                [
                    'attribute' => 'blocked',
                    'value' => $model->getBlocked(),
                ],
                'createdAt:datetime',
                'updatedAt:datetime',
            ],
        ]) ?>

        <div class="col-lg">
            <div class="panel panel-default">
                <div class="panel-heading">Роли</div>

                <div class="panel-body">
                    <div class="list-group">
                        <?php foreach (array_intersect_key($roles, $model->roles) as $role) : ?>
                            <a class="list-group-item">
                                <?= $role ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
