<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 03.08.17
 * Time: 10:44
 */

use app\modules\opendata\assets\OpendataAsset;
use app\modules\opendata\dto\OpendataStatDto;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $formats [] */
/** @var $passportList \app\modules\opendata\models\OpendataPassport[] */
/** @var $passportArchiveList \app\modules\opendata\models\OpendataPassport[] */
/** @var $rosterStat \app\modules\opendata\dto\OpendataStatDto */
/** @var $rosterSizes array */
/** @var $passportStat \app\modules\opendata\dto\OpendataStatDto[] */
/** @var $formModel \app\modules\opendata\forms\CommentForm */

OpendataAsset::register($this);

$this->title = 'Открытые данные';
$this->params['breadcrumbs'] = [
    ['label' => $this->title],
];
$this->params['share-page'] = true;

$totalPassportStatShows = $totalPassportStatDownloads = 0;
?>
<div class="clearfix">
    <div class="main">
        <h1 class="page-title text-black"><?= $this->title; ?></h1>
        <div class="pd-bottom-70 pd-top-30">
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="search-form clearfix pd-bottom-30">
                            <div class="col-xs-12">
                                <h4 class="text-black">Искать в открытых данных</h4>
                            </div>
                            <form action="" class="form form-comment contains" data-where="list-dataset">
                                <div class="col-xs-12">
                                    <div class="form-search-row">
                                        <div class="form-search-row__field form-group form-group--placeholder-fix">
                                            <label class="placeholder" for="search-phrase">Введите фразу для
                                                поиска</label>
                                            <input type="text" id="search-phrase" class="phrase form-control"
                                                   name="search"
                                                   value="">
                                        </div>
                                        <div class="form-search-row__btn">
                                            <button type="submit" class="btn-block btn btn-primary btn-lg">Найти
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($passportList): ?>
                <div class="row">
                    <div class="col-xs-12">

                        <ul class="list-dataset" id="list-dataset">
                            <?php foreach ($passportList as $model):
                                /** @var OpendataStatDto $stat */
                                $stat = ArrayHelper::getValue($passportStat, $model->id, new OpendataStatDto());
                                $totalPassportStatShows += $stat->getShows();
                                $totalPassportStatDownloads += $stat->getDownloads();
                                ?>
                                <li>
                                    <div class="list-dataset__head">
                                        <div class="date">
                                            <?= Yii::$app->formatter->asDate(
                                                $model->set->updated_at,
                                                'dd MMMM yyyy'
                                            ) ?> года
                                        </div>
                                        <div class="info-box-wrap">
                                            <div class="info-box-border">
                                                <span>просмотров:</span>
                                                <?= Yii::$app->formatter->asDecimal(
                                                    $stat->getShows()
                                                ); ?>
                                            </div>
                                            <div class="info-box-border">
                                                <span>скачиваний:</span>
                                                <?= Yii::$app->formatter->asDecimal(
                                                    $stat->getDownloads()
                                                ) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="list-dataset__name text-black-b"
                                       href="<?= Url::to(['/opendata/passport', 'id' => $model->id]); ?>"><i
                                                class="list-num"></i><?= $model->title; ?></a>
                                    <div class="list-dataset__download">
                                        <?php if (count($formats) === 1): ?>
                                            <?php
                                            $ext = reset($formats);
                                            ?>
                                            <div class="download-box bootstrap-select btn-group">
                                                <a href="<?= Url::to([
                                                    '/opendata/passport-meta',
                                                    'ext' => $ext,
                                                    'id' => $model->id,
                                                ]); ?>"
                                                   class="btn-default btn list-dataset__download-link dropdown-toggle">
                                                    <span class="filter-option">Паспорт <?= strtoupper($ext); ?></span>
                                                    <span class="bs-caret"><span class="caret"></span></span>
                                                </a>
                                            </div>
                                            <div class="download-box bootstrap-select btn-group">
                                                <a href="<?= Url::to([
                                                    '/opendata/data',
                                                    'ext' => $ext,
                                                    'version' => $model->set->version,
                                                    'data-time' => $model->set->getVersionDate(),
                                                    'id' => $model->id,
                                                ]); ?>"
                                                   class="btn-default btn list-dataset__download-link dropdown-toggle">
                                                    <span class="filter-option">Набор <?= strtoupper($ext); ?></span>
                                                    <span class="bs-caret"><span class="caret"></span></span>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <select class="selectpicker download-box" title="Паспорт">
                                                <?php foreach ($formats as $ext): ?>
                                                    <option data-url="<?= Url::to([
                                                        '/opendata/passport-meta',
                                                        'ext' => $ext,
                                                        'id' => $model->id,
                                                    ]); ?>">Паспорт <?= strtoupper($ext); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select class="selectpicker download-box" title="Скачать набор">
                                                <?php foreach ($formats as $ext): ?>
                                                    <option data-url="<?= Url::to([
                                                        '/opendata/data',
                                                        'ext' => $ext,
                                                        'version' => $model->set->version,
                                                        'data-time' => $model->set->getVersionDate(),
                                                        'id' => $model->id,
                                                    ]); ?>">Набор <?= strtoupper($ext); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($passportArchiveList): ?>
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-title text-black">Архив</h1>
                        <ul class="list-dataset" id="list-dataset-archive">
                            <?php foreach ($passportArchiveList as $model):
                                /** @var OpendataStatDto $stat */
                                $stat = ArrayHelper::getValue($passportStat, $model->id, new OpendataStatDto());
                                ?>
                                <li>
                                    <div class="list-dataset__head">
                                        <div class="date">
                                            <?= Yii::$app->formatter->asDate(
                                                $model->set->updated_at,
                                                'dd MMMM yyyy'
                                            ); ?> года
                                        </div>
                                        <div class="info-box-wrap">
                                            <div class="info-box-border">
                                                <span>просмотров:</span> <?= Yii::$app->formatter->asDecimal(
                                                    $stat->getShows()
                                                ) ?></div>
                                            <div class="info-box-border">
                                                <span>скачиваний:</span> <?= Yii::$app->formatter->asDecimal(
                                                    $stat->getDownloads()
                                                ) ?></div>
                                        </div>
                                    </div>
                                    <a class="list-dataset__name text-black-b"
                                       href="<?= Url::to(['/opendata/passport', 'id' => $model->id]); ?>">
                                        <i class="list-num"></i><?= $model->title; ?>
                                    </a>
                                    <div class="list-dataset__download">
                                        <!-- нужно будет дописать условие. Если только один формат файла - вместо выпадашки ставим ссылку, разметка ниже -->
                                        <!-- <div class="download-box bootstrap-select btn-group">
                                            <a href="" class="btn-default btn list-dataset__download-link dropdown-toggle"><span class="filter-option">Паспорт CSV</span>
                                            <span class="bs-caret"><span class="caret"></span></span>
                                            </a>
                                        </div> -->
                                        <!-- если больше - выводим в выпадашку-->
                                        <select class="selectpicker download-box" title="Паспорт">
                                            <?php foreach ($formats as $ext): ?>
                                                <option data-url="<?= Url::to([
                                                    '/opendata/passport-meta',
                                                    'ext' => $ext,
                                                    'id' => $model->id,
                                                ]); ?>">Паспорт <?= strtoupper($ext); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select class="selectpicker download-box" title="Скачать набор">
                                            <?php foreach ($formats as $ext): ?>
                                                <option data-url="<?= Url::to([
                                                    '/opendata/data',
                                                    'ext' => $ext,
                                                    'version' => $model->set->version,
                                                    'data-time' => $model->set->getVersionDate(),
                                                    'id' => $model->id,
                                                ]); ?>">Набор <?= strtoupper($ext); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (empty($passportArchiveList) && empty($passportList)): ?>
                <div class="row">
                    <div class="col-xs-12">
                        <p>Нет данных</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <aside class="main-aside">
        <ul class="list-nav list-nav--light hidden-xs hidden-sm">
            <li><a class="text-dark" href="/ministry/opengov/0">Принципы работы системы «Открытое министерство»</a></li>
            <li class="active"><a class="text-dark" href="/opendata">Открытые данные</a></li>
            <li><a class="text-dark" href="/ministry/opengov/1">Проектный центр Минтруда России</a></li>
            <li><a class="text-dark" href="/ministry/opengov/15">Публичная декларация</a></li>
            <li><a class="text-dark" href="/ministry/opengov/2">Планы и программа работ</a></li>
            <li><a class="text-dark" href="/ministry/opengov/4">Общественный совет</a></li>
            <li><a class="text-dark" href="/ministry/opengov/10">Обсуждение проектов нормативных правовых актов</a></li>
            <li><a class="text-dark" href="/ministry/opengov/11">Доклады о результатах и основных направлениях
                    деятельности
                    Минтруда России</a></li>
            <li><a class="text-dark" href="/ministry/opengov/12">Работа с референтными группами</a></li>
            <li><a class="text-dark" href="/ministry/opengov/13">Планы законопроектной деятельности</a></li>
            <li><a class="text-dark" href="/ministry/opengov/14">План деятельности Министерства труда и социальной
                    защиты
                    Российской Федерации на 2013-2018 годы</a></li>
        </ul>
        <div class="border-block">
            <h3 class="text-black text-center">Реестр открытых данных</h3>
            <?php foreach ($formats as $ext): ?>
                <a href="<?= Url::to(['/opendata/list', 'ext' => $ext]) ?>"
                   class="btn btn-block btn-primary">Скачать</a>
                <div class="under-btn-info">
                    <span>Формат: <?= strtoupper($ext); ?></span>
                    <span>Размер: <?= Yii::$app->formatter->asShortSize(
                            ArrayHelper::getValue($rosterSizes, $ext, 0)
                        ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="border-block block-arrow">
            <h3 class="text-black">Статистика</h3>
            <p class="pd-bottom-15"><span class="text-light">Количество наборов открытых данных:</span>
                <span><?= count($passportList); ?></span></p>
            <p class="pd-bottom-15"><span class="text-light">Количество просмотров наборов:</span>
                <span><?= Yii::$app->formatter->asDecimal($totalPassportStatShows); ?></span></p>
            <p class="pd-bottom-15"><span class="text-light">Количество загрузок наборов:</span>
                <span><?= Yii::$app->formatter->asDecimal($totalPassportStatDownloads); ?>
                </span>
            </p>

            <p class="pd-bottom-15"><span class="text-light">Количество просмотров реестра открытых данных:</span>
                <span><?= Yii::$app->formatter->asDecimal($rosterStat->getShows()); ?></span>
            </p>
            <p class="pd-bottom-0"><span class="text-light">Количество загрузок файла реестра:</span>
                <span><?= Yii::$app->formatter->asDecimal($rosterStat->getDownloads()); ?>
                </span>
            </p>
        </div>
        <div class="border-block">
            <h3 class="text-black text-center">Есть вопросы, предложения или пожелания?</h3>
            <a data-toggle="modal" data-target="#modalFeedback" href="#" class="btn btn-block btn-primary">
                Напишите нам
            </a>
        </div>
    </aside>
</div>

<?= $this->render('_popup', ['formModel' => $formModel]); ?>
