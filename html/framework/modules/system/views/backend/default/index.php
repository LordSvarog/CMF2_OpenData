<?php

/* @var $this yii\web\View */

use krok\auth\models\Log;
use krok\paperdashboard\widgets\analytics\AnalyticsWidget;
use krok\paperdashboard\widgets\analytics\SpaceCircleChartWidget;
use krok\paperdashboard\widgets\welcome\WelcomeWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = 'Администрирование';
?>
<div class="row">
    <div class="style-card">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-md-12">
                    <?= WelcomeWidget::widget([
                        'login' => ArrayHelper::getValue(Yii::$app->getUser()->getIdentity(), 'login'),
                        'url' => Url::to(['/auth/profile']),
                        'lastLoginAt' => Yii::$app->getFormatter()->asDatetime(Log::getLastLoginAt(Yii::$app->getUser()->getIdentity()->getId()),
                            'dd MMMM YYYY г., HH:mm'),
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="two-card-info">
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Общие характеристики системы</h4>
                            </div>
                            <div class="card-content">
                                <ul class="system-info-list">
                                    <li>
                                        <?= AnalyticsWidget::widget(['name' => 'os/version', 'constructor' => ['s']]) ?>
                                    </li>
                                    <li>
                                        <?= AnalyticsWidget::widget(['name' => 'database/info']) ?>
                                    </li>
                                    <li>
                                        <?= AnalyticsWidget::widget(['name' => 'php/version']) ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <?= SpaceCircleChartWidget::widget() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
