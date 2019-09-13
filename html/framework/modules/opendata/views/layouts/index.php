<?php

use app\assets\AppAsset;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $content string */

AppAsset::register($this);
$this->registerMetaTag(['property' => 'og:url', 'content' => Yii::$app->request->absoluteUrl]);
$this->registerMetaTag(['property' => 'og:title', 'content' => $this->title]);
if (!array_key_exists('og:description', $this->metaTags)) {
    $this->registerMetaTag(['property' => 'og:description', 'content' => ''], 'og:description');
}
if (!array_key_exists('og:image', $this->metaTags)) {
    $this->registerMetaTag([
        'property' => 'og:image',
        'content' => Yii::$app->request->hostInfo . '/static/default/img/images/logo-social.png',
    ], 'og:image');
}

$this->beginPage();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN">
<html xmlns="http://www.w3.org/1999/xhtml"
      xml:lang="<?= Yii::$app->language ?>"
      version="XHTML1+RDFa 1.1" dir="ltr">
<head>
    <meta charset="<?= Html::encode($this->title) ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('//parts/header') ?>
<?= $this->render('//parts/breadcrumbs') ?>

<!-- page content -->
<section class="pd-top-0 pd-bottom-30">
    <div class="container">
        <?= $content ?>
    </div>
</section>
<!-- page content end -->

<?= $this->render('//parts/footer') ?>

<?php if ($this->blocks):
    foreach ($this->blocks as $block):
        print $block;
    endforeach;
endif; ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage(); ?>
