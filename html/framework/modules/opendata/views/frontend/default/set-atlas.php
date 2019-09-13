<?php

use app\modules\opendata\assets\OpendataAtlasAsset;

/* @var $this yii\web\View */
/* @var $model \app\modules\opendata\models\OpendataSet */
/* @var $data array */

$this->title = $model->passport->title;

$this->params['breadcrumbs'] = [
    ['label' => 'Открытые данные', 'url' => ['/opendata']],
    ['label' => $this->title],
];

$this->params['share-page'] = true;

OpendataAtlasAsset::register($this);

if ($model->hasMap()) {
    $this->registerJs(new \yii\web\JsExpression('
        $("#map").atlas(window.map.paths, {
            data: ' . json_encode($data) . '    
        });
    '));
}
?>


<div class="clearfix">
    <div class="col-md-12 pd-bottom-60" style="width: 100%">
        <h1 class="page-title text-black"><?= $this->title ?></h1>
        <div class="pd-bottom-70 pd-top-30">
            <div class="map-box">
                <div class="wrapper">
                    <?php if ($model->hasMap()): ?>
                        <div id="map"></div>
                    <?php else: ?>
                        Карта регионов для данного набора отсутствует.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="regionPopupTemplate" type="text/template">
    <div class="point gradient text-black">
        <h2>
            <span class="tts_region"><%= region %></span>
        </h2>
        <div class="content-wrap">
            <table>
                <% for(var i in data){ %>
                <tr class="table-row value">
                    <th class="rate-title title"><%= data[i].title %></th>
                    <td class="digit"><%= data[i].value %></td>
                </tr>
                <% } %>
            </table>
        </div>
    </div>
</script>