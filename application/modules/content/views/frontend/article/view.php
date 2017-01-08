<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\content\models\ContentArticles;
 * @var $categoryModel \app\modules\content\models\ContentCategory;
 * @var $breadcrumbs \yii\widgets\Breadcrumbs;
 */
use app\modules\admin\widgets\Html;

$this->setSeoData(($model->meta_title != '')?$model->meta_title:$model->title, $model->meta_description, $model->meta_keywords);
?>

<section class="sctn-project cgo">
    <div class="h2-prjct container-fluid">
        <div class="container">
            <h2 class="h2-red"><?= $categoryModel->title ?></h2>
        </div>
    </div>
    <div class="line-block">
        <div class="container">
            <div class="line-block__right">
                <p><?= Yii::$app->formatter->asDate($model->published_at, 'long') ?></p>
            </div>
        </div>
    </div>
    <article class="news-in container">
        <div class="news-in__ttl container">
            <div class="col-sm-8 news-in__ttl_txt clearfix">
                <?= $model->title ?>
            </div>
        </div>
        <div class="news-in__header container">
            <div class="col-sm-8 news-in__header__img">
                <img src="<?= $model->getUploadUrl('image') ?>" alt="">
            </div>
            <div class="col-sm-4 news-in__header__desc">
                <span>
                    <?= $model->description ?>
                </span>
            </div>
        </div>
        <div class="container news-in__content">
            <div class="news-in__content__txt">
               <?= $model->content ?>
            </div>
            <?if(count($model->medias)):?>
            <div class="news-in__content__img">
                <?php
                $count = 0;
                foreach ($model->medias as $media):?>
                    <div class="col-sm-6 <?= ++$count%2 ? "lft-img" : "rght-img" ?>">
                        <img src="<?= $media->file_url ?>" alt="<?= $media->file_title ?>">
                    </div>
                    <? if(($count%2) == 0): ?>
                        <div class="clearfix"></div>
                        <br>
                    <?endif;?>
                <?endforeach;?>
            </div>
            <?endif;?>
        </div>
    </article>
</section>

<section class="pag-nav">
    <div class="container pag-con">
        <nav class="pag-con__nav" aria-label="Page navigation">
            <ul class="pager news">
                <?if($model->getPrevious()):?>
                <li class="pre-nav">
                    <?= Html::a('&lt;&lt;&lt; '.Yii::t('content', 'previous news'), ['/content/article/view', 'catslug' => $model->previous->category->slug, 'slug' => $model->previous->slug]) ?>
                </li>
                <?endif?>
                <?if($model->getNext()):?>
                <li class="next-nav">
                    <?= Html::a(Yii::t('content', 'next news').' &gt;&gt;&gt;', ['/content/article/view', 'catslug' => $model->next->category->slug, 'slug' => $model->next->slug]) ?>
                </li>
                <?endif?>
            </ul>
        </nav>
    </div>
</section>
