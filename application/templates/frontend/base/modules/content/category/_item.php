<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.09.2016
 * Project: osnovasite
 * File name: _item.php
 * @var $model \app\modules\content\models\ContentArticles;
 */


use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\StringHelper;
?>
<a href="<?= Url::to($model->getFrontendViewLink()) ?>">
    <div class="image-holder">
        <img src="<?= $model->getThumbUploadUrl('image') ?>" alt="Все о работе на стройках: очень хорошая  зарплата и  работа" class="fake-img">
        <div style="background: url(<?= $model->getThumbUploadUrl('image') ?>) no-repeat;
            background-position: center ;
            background-repeat: no-repeat;
            background-size: cover;"
             class="img"></div>
    </div>
</a>
<h4>
    <?= Html::a($model->title, $model->getFrontendViewLink())?></h4>
<div class="block-info-article inline-layout">
    <time><?= Yii::$app->formatter->asDate($model->published_at, 'php:d-m-Y'); ?></time>
    <p class="view-user">15 пользователей</p>
</div>
<div class="block_text">
    <p><?= StringHelper::truncateWords(strip_tags($model->content), 60) ?></p>
</div>

<?= Html::a(Yii::t('catalog', 'Read more &gt;'), $model->getFrontendViewLink(), ['class' => 'link_more']) ?>


