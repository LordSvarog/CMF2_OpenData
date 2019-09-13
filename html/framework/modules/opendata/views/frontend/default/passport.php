<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 03.08.17
 * Time: 12:26
 */

use app\modules\opendata\assets\OpendataAsset;
use app\modules\opendata\helper\ConvertEntitiesHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $model \app\modules\opendata\models\OpendataPassport */
/** @var $inn string */
/** @var $formats array */
/** @var $passportStat \app\modules\opendata\dto\OpendataStatDto */
/** @var $setStat array */
/** @var $formModel \app\modules\opendata\forms\CommentForm */
/** @var $rating \app\modules\opendata\models\OpendataRating */
/** @var $updatedAt integer */
/** @var $createdAt integer */


$this->title = $model->title;
$this->params['breadcrumbs'] = [
    ['label' => 'Открытые данные', 'url' => ['/opendata']],
    ['label' => $this->title],
];
$this->params['share-page'] = true;

OpendataAsset::register($this);

$this->registerMetaTag([
    'property' => 'og:description',
    'content' => preg_replace("#([\r\n\t\s]+)#", ' ', $model->description),
], 'og:description');
?>

<div class="clearfix">
    <div class="main">
        <h1 class="page-title text-black"><?= $this->title; ?></h1>
        <div class="pd-bottom-30 pd-top-30">
            <ul class="list-table-style text-black">
                <li class="list-table-style__head">
                    <div class="list-table-style__col">Характеристика</div>
                    <div class="list-table-style__col">Описание</div>
                </li>
                <li>
                    <div class="list-table-style__col">Идентификационный номер</div>
                    <div class="list-table-style__col" property="dc:identifier">
                        <?= $inn; ?>-<?= $model->code; ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Наименование набора открытых данных</div>
                    <div class="list-table-style__col" property="dc:title">
                        <?= ConvertEntitiesHelper::convert($model->title); ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Описание набора открытых данных</div>
                    <div class="list-table-style__col" property="dc:description">
                        <?= ConvertEntitiesHelper::convert($model->description); ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Владелец набора данных</div>
                    <div class="list-table-style__col" property="dc:owner">
                        <?= ConvertEntitiesHelper::convert($model->owner); ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Ответственное лицо</div>
                    <div class="list-table-style__col">
                        <?= $model->publisher_name; ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Телефон ответственного лица</div>
                    <div class="list-table-style__col">
                        <a href="tel:<?= $model->publisher_phone; ?>"><?= $model->publisher_phone; ?></a>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Адрес эл. почты ответственного лица</div>
                    <div class="list-table-style__col"><?= $model->publisher_email; ?></div>
                </li>
                <li>
                    <div class="list-table-style__col">
                        Гиперссылка (URL) на последний опубликованный набор данных
                    </div>
                    <div class="list-table-style__col">
                        <?php foreach ($formats as $ext): ?>
                            <a href="<?= Url::to([
                                '/opendata/data',
                                'ext' => $ext,
                                'version' => $model->set->version,
                                'data-time' => $model->set->getVersionDate(),
                                'id' => $model->id,
                            ], true); ?>">
                                <?= Url::to([
                                    '/opendata/data',
                                    'ext' => $ext,
                                    'version' => $model->set->version,
                                    'data-time' => $model->set->getVersionDate(),
                                    'id' => $model->id,
                                ], true); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Формат публикации</div>
                    <div class="list-table-style__col"><?= implode(', ', $formats); ?></div>
                </li>
                <li>
                    <div class="list-table-style__col">Ссылка на файл структуры данных</div>
                    <div class="list-table-style__col">
                        <?php foreach ($formats as $ext): ?>
                            <a href="<?= Url::to([
                                '/opendata/data-schema',
                                'ext' => ($ext == 'xml' ? 'xsd' : $ext),
                                'version' => $model->set->version,
                                'id' => $model->id,
                            ], true); ?>">
                                <?= Url::to([
                                    '/opendata/data-schema',
                                    'ext' => ($ext == 'xml' ? 'xsd' : $ext),
                                    'version' => $model->set->version,
                                    'id' => $model->id,
                                ], true); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Дата первой публикации набора</div>
                    <div class="list-table-style__col" property="dc:created">
                        <?= Yii::$app->formatter->asDate($createdAt, 'dd.MM.yyyy'); ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Дата последних внесенных изменений</div>
                    <div class="list-table-style__col" property="dc:modified">
                        <?= Yii::$app->formatter->asDate($updatedAt, 'dd.MM.yyyy'); ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Содержание последнего изменения</div>
                    <div class="list-table-style__col"><?= $model->set->changes; ?></div>
                </li>
                <li>
                    <div class="list-table-style__col">Версия методических рекомендаций</div>
                    <div class="list-table-style__col">3.0</div>
                    <div></div>
                </li>
                <li>
                    <div class="list-table-style__col">Периодичность актуализации набора данных</div>
                    <div class="list-table-style__col" property="dc:valid" content="<?= $model->set->updated_at; ?>">
                        <?= ConvertEntitiesHelper::convert($model->update_frequency); ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Теги</div>
                    <div class="list-table-style__col">
                        <?php foreach ($model->getTags() as $tag): ?>
                            <span class="tag"><?= $tag; ?></span>
                        <?php endforeach; ?>
                </li>
                <li>
                    <div class="list-table-style__col">Гиперссылки (URL) на версии набора данных</div>
                    <div class="list-table-style__col">
                        <?php
                        $opendataSets = $model->opendataSets;
                        array_shift($opendataSets);
                        foreach ($opendataSets as $set): ?>
                            <?php foreach ($formats as $ext): ?>
                                <a href="<?= Url::to([
                                    '/opendata/data',
                                    'ext' => $ext,
                                    'version' => $set->version,
                                    'data-time' => $set->getVersionDate(),
                                    'id' => $model->id,
                                ], true); ?>">
                                    <?= Url::to([
                                        '/opendata/data',
                                        'ext' => $ext,
                                        'version' => $set->version,
                                        'data-time' => $set->getVersionDate(),
                                        'id' => $model->id,
                                    ], true); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li>
                    <div class="list-table-style__col">Гиперссылки (URL) на версии структуры набора данных
                    </div>
                    <div class="list-table-style__col">
                        <?php foreach ($opendataSets as $set): ?>
                            <?php foreach ($formats as $ext): ?>
                                <a href="<?= Url::to([
                                    '/opendata/data-schema',
                                    'ext' => ($ext == 'xml' ? 'xsd' : $ext),
                                    'version' => $set->version,
                                    'id' => $model->id,
                                ], true); ?>">
                                    <?= Url::to([
                                        '/opendata/data-schema',
                                        'ext' => ($ext == 'xml' ? 'xsd' : $ext),
                                        'version' => $set->version,
                                        'id' => $model->id,
                                    ], true); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </li>
            </ul>
        </div>

        <div class="info-link-block">
            <a href="<?= Url::to(['terms']); ?>" target="_blank"><i class="icon-info-2"></i><span>Условия использования открытых данных</span></a>
        </div>

        <div class="dataset-btn-wrap row">
            <?php if ($model->set->hasTable()): ?>
                <div class="col-md-12 col-lg-6 col-sm-6 col-xs-12">
                    <a href="<?= Url::to(['set-data', 'id' => $model->set->id]) ?>"
                       class="btn-block btn btn-lg btn--icon-left btn-border btn-border--blue-text"><i
                                class="icon-table"></i><span>Табличное представление</span></a>
                </div>
            <?php endif; ?>
            <?php if ($model->set->hasChart()): ?>
                <div class="col-md-12 col-lg-6 col-sm-6 col-xs-12">
                    <a href="<?= Url::to(['set-chart', 'id' => $model->set->id]) ?>"
                       class="btn-block btn btn-lg btn--icon-left btn-border btn-border--blue-text"><i
                                class="icon-garphic"></i><span>Графическое представление</span></a>
                </div>
            <?php endif; ?>
            <?php if ($model->set->hasMap()): ?>
                <div class="col-md-12 col-lg-6 col-sm-6 col-xs-12">
                    <a href="<?= Url::to(['set-atlas', 'id' => $model->set->id]) ?>"
                       class="btn-block btn btn-lg btn--icon-left btn-border btn-border--blue-text"><i
                                class="icon-map"></i><span>Карта регионов</span></a>
                </div>
            <?php endif; ?>
        </div>

        <?php /*
        <div class="bg-gray bg-box pd-top-35 pd-bottom-60 download-block">
            <div class="container-fluid">
                <div class="row">
                    <div class="gray-bg__big-title text-black text-center">Сохранить данные набора</div>
                </div>
                <?php foreach ($formats as $format): ?>
                    <div class="row">
                        <div class="col-lg-6 col-pd-wide">
                            <a href="<?= Url::to([
                                '/opendata/data',
                                'ext' => $format,
                                'version' => $model->set->version,
                                'data-time' => $model->set->getVersionDate(),
                                'id' => $model->id,
                            ]); ?>" class="btn btn-primary btn-md btn-block">Скачать набор</a>
                            <div class="under-btn-info">
                                <span>Формат: <?= strtoupper($format); ?></span>
                                <span>Размер: <?= Yii::$app->formatter->asShortSize(
                                        ArrayHelper::getValue($setStat,
                                            $format . '.size', 0)
                                    ); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-pd-wide">
                            <a href="<?= Url::to([
                                '/opendata/passport-meta',
                                'id' => $model->id,
                                'ext' => $format,
                            ]) ?>" class="btn btn-primary btn-md btn-block">Скачать паспорт</a>
                            <div class="under-btn-info">
                                <span>Формат: <?= strtoupper($format); ?></span>
                                <span>Размер: <?= Yii::$app->formatter->asShortSize(
                                        ArrayHelper::getValue($passportStat,
                                            $format . '.size', 0)
                                    ); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
 */ ?>

        <!-- RDFa definitions -->
        <div class="hidden">
            <div resource="#publisher" rel="dc:publisher"></div>
            <div resource="#data" rel="dc:source"></div>
            <div property="dc:subject">
                <?= ConvertEntitiesHelper::convert($model->subject); ?>
            </div>
            <div about="#publisher" typeof="foaf:Person">
                <div property="foaf:name"><?= ConvertEntitiesHelper::convert($model->publisher_name); ?></div>
                <div content="<?= $model->publisher_phone; ?>" property="foaf:phone"></div>
                <div content="<?= $model->publisher_email; ?> " property="foaf:mbox"></div>
            </div>
            <div about="#data" typeof="dc:Collection">
                <?php foreach ($model->opendataSets as $set): ?>
                    <?php foreach ($formats as $ext): ?>
                        <div resource="#data-<?= $set->getVersionDate() ?>-<?= $ext; ?>" rel="dc:hasPart">
                            <div about="#data-<?= $set->getVersionDate(); ?>-<?= $ext; ?>" typeof="foaf:Document">
                                <div content="<?= Url::to([
                                    '/opendata/data',
                                    'ext' => $ext,
                                    'version' => $set->id,
                                    'data-time' => $set->getVersionDate(),
                                    'id' => $model->id,
                                ], true); ?>"
                                     property="dc:source"></div>
                                <div content="<?= strtoupper($ext); ?>" property="dc:format"></div>
                                <div property="dc:created">
                                    <?= Yii::$app->formatter->asDate($set->created_at,
                                        'dd.MM.yyyy'); ?>
                                </div>
                                <div property="dc:provenance">
                                    <?= ConvertEntitiesHelper::convert($set->title); ?>
                                </div>
                                <div resource="#structure-<?= $set->id; ?>-<?= $ext; ?>" rel="dc:conformsTo">&nbsp;
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
            <div>
                <div class="structure-files">
                    <?php foreach ($model->opendataSets as $set): ?>
                        <?php foreach ($formats as $ext): ?>
                            <div typeof="foaf:Document" about="#structure-<?= $set->id; ?>-<?= $ext; ?>">
                                <div property="dc:source"
                                     content="<?= Url::to([
                                         '/opendata/data-schema',
                                         'ext' => ($ext == 'xml' ? 'xsd' : $ext),
                                         'version' => $set->id,
                                         'id' => $model->id,
                                     ], true); ?>"></div>
                                <div property="dc:format"
                                     content="<?= strtoupper($ext == 'xml' ? 'xsd' : $ext); ?>"></div>
                                <div property="dc:created" content="<?= Yii::$app->formatter->asDate($set->created_at,
                                    'dd.MM.yyyy'); ?>"></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- /RDFa definitions -->
    </div>
    <aside class="main-aside">
        <ul class="list-nav list-nav--light hidden-xs hidden-sm">
            <li><a class="text-dark" href="#">Принципы работы системы «Открытое министерство»</a></li>
            <li class="active"><a class="text-dark" href="#">Открытые данные</a></li>
            <li><a class="text-dark" href="#">Проектный центр Минтруда России</a></li>
            <li><a class="text-dark" href="#">Публичная декларация</a></li>
            <li><a class="text-dark" href="#">Планы и программа работ</a></li>
            <li><a class="text-dark" href="#">Общественный совет</a></li>
            <li><a class="text-dark" href="#">Обсуждение проектов нормативных правовых актов</a></li>
            <li><a class="text-dark" href="#">Доклады о результатах и основных направлениях деятельности Минтруда
                    России</a></li>
            <li><a class="text-dark" href="#">Работа с референтными группами</a></li>
            <li><a class="text-dark" href="#">Планы законопроектной деятельности</a></li>
            <li><a class="text-dark" href="#">План деятельности Министерства труда и социальной защиты Российской
                    Федерации на 2013-2018 годы</a></li>
        </ul>
        <div class="border-block block-arrow">
            <p class="pd-bottom-15">
                <span class="text-light">Просмотров:</span> <span><?= Yii::$app->formatter->asDecimal(
                        $passportStat->getShows()
                    ); ?></span>
            </p>
            <p class="pd-bottom-15">
                <span class="text-light">Скачиваний:</span> <span>
                    <?= Yii::$app->formatter->asDecimal($passportStat->getDownloads()); ?>
                    </span>
            </p>

            <div class="rating-container<?php if (!$rating): ?> hidden<?php endif; ?>">
                <p class="pd-bottom-15">
                    <span class="text-light">Голосов:</span> <span class="count"><?= ArrayHelper::getValue($rating,
                            'count', 0); ?></span>
                </p>
                <p class="pd-bottom-0">
                    <span class="text-light">Оценка полезности:</span> <span
                            class="rating"><?= ArrayHelper::getValue($rating, 'count', 0); ?></span>
                </p>
            </div>
        </div>
        <?= \app\modules\opendata\widgets\OpendataRatingWidget::widget([
            'passport_id' => $model->id,
        ]); ?>
        <div class="border-block">
            <h3 class="text-black text-center">Есть вопросы, предложения или пожелания?</h3>
            <a data-toggle="modal" data-target="#modalFeedback" href="#" class="btn btn-block btn-primary">Напишите
                нам</a>
        </div>
    </aside>
</div>

<?= $this->render('_popup', ['model' => $model, 'formModel' => $formModel]); ?>
