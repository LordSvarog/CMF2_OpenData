<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 08.08.17
 * Time: 18:20
 */

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\modules\opendata\models\OpendataPassport */
/* @var $form \app\modules\opendata\forms\CommentForm */
?>

<p>
    Пользователь <?= $form->name; ?> (<?= $form->email; ?>) оставил комментарий
    для <?php if ($model): ?>набора открытых данных:<?php else: ?>открытых данных.<?php endif; ?>
    <?php if ($model): ?>
        <a href="<?= Url::to(['/opendata/passport', 'id' => $model->id], true); ?>">
            <?= $model->title; ?>
        </a>
    <?php endif; ?>
</p>
<p>
    Комментарий:<br/>
    <?= nl2br($form->comment); ?>
</p>
