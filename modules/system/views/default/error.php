<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;

?>
<div>
    <div class="mb-100"></div>
    <?php
    if($exception->statusCode == 404) {
        ?>
        <div class="mb-30 font-22" style="text-transform: uppercase"><?= Yii::t('app', 'Error 404! Page not found.') ?></div>
        <div class="line mb-20">
            <img src="/img/404.png" alt="404">
        </div>
        <?
    } else {
        ?>
        <div class="mb-30 font-20" style="text-transform: uppercase"><?= Html::encode($this->title) ?></div>
        <div class="line mb-20">
            <div class="font-40"><?= Html::encode($this->title) ?></div>
        </div>
        <?
    }
    ?>
    <div class="font-22">
        <?= nl2br(Yii::t('app', "This sometimes happens :( \n The most likely reasons for this - an outdated link or the page has been removed by the author.")) ?>
    </div>
    <div class="font-22">
        <?= Html::a(Yii::t('app', 'Go back to the home page'), \yii\helpers\Url::to(['site/index']), ['style'=>'color: #fff;text-decoration:underline']) ?>
    </div>
</div>
