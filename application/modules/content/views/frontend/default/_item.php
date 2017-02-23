<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 30.08.2016
 * Project: falconcity
 * File name: _item.php
 *
 * @var $model \app\modules\content\models\ContentArticles;
 */
?>

<div class="news-block image-left">

    <div class="item">
        <div class="title">
            <?= $model->title ?>
        </div>
        <div class="datetime">
            <div class="icon-c icon-c-calendar"></div>
            <span><?= date('d.m.Y', $model->published_at) ?></span>
        </div>
        <div class="news-block__image col-sm-4">
            <div class="row">
                <div class="news-block__image__img nbg">
                    <img src="<?= $model->getThumbUploadUrl('image') ?>" class="img-responsive" alt="">
                </div>
            </div>
        </div>

        <div class="news-block__content col-sm-8">
            <div class="news-block__content<?=(iconv_strlen((str_replace(' ', '', strip_tags($model->content))), 'UTF-8') > 450)?'__two-column':''?>">
                <?= $model->content ?>
                <!--<a href="#" class="news-block__readmore white">Подробнее</a>-->
            </div>
        </div>
    </div>
</div>
