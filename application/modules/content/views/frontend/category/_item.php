<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.09.2016
 * Project: osnovasite
 * File name: _item.php
 * @var $model \app\modules\content\models\ContentArticles;
 */
?>
<div class="container">
    <article class="main-news-article wow fadeIn" data-wow-offset="50" data-wow-duration="1.5s" data-wow-delay="0s">
        <div class="main-news-article__image col-sm-4">
            <img src="<?= $model->getUploadUrl('image') ?>" alt="<?= $model->title ?>">
        </div>
        <div class="main-news-article__text col-sm-8">
            <h4><?= $model->title ?></h4>
            <p><?= \yii\helpers\StringHelper::truncateWords(strip_tags($model->content), 20) ?></p>
            <?= \app\modules\admin\widgets\Html::a(Yii::t('text', 'Read more...'), ['/content/article/view', 'catslug' => $model->category->slug, 'slug'=>$model->slug])?>
        </div>
    </article>
</div>
