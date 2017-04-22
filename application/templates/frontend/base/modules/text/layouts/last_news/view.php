<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\text\models\Text;
 */

use yii\helpers\Url;
use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentArticlesLang;

$news = ContentArticles::find()
    ->joinWith(['translations'])
    ->andWhere([
        ContentArticles::tableName().'.category_id'  => $model->getSetting('newsCategory'),
        ContentArticles::tableName().'.status'       => ContentArticles::STATUS_PUBLISHED,
        ContentArticlesLang::tableName().'.language' => Yii::$app->language
    ])
    ->orderBy(['published_at' => SORT_DESC])
    ->limit(4)
    ->all();
?>
<?php if(count($news) > 0): ?>

<div class="block-last-news wow fadeIn">
<?php if($model->getSetting('showWrapper')):?>
    <div class="container">
<?php endif; ?>
    <?= \yii\helpers\Html::tag($model->getSetting('tag', 'h3'), $model->getSetting('title')) ?>
    <?php if($model->getSetting('description') != ""): ?>
    <div class="block_desc">
        <p><?= $model->getSetting('description') ?></p>
    </div>
    <?php endif; ?>
    <div class="list-last-news">
        <div class="inline-layout col-2">
            <?php foreach ($news as $new): ?>
                <div class="item">
                    <a href="<?= Url::to($new->frontendViewLink) ?>">
                        <h4><?= $new->title ?></h4>
                        <div class="block-info-article inline-layout">
                            <time><?= date('d.m.Y', $new->published_at) ?></time>
                            <p class="view-user"><?= Yii::t(
                                    'catalog',
                                    '{n, plural, one{# user} other{# users}}',
                                    ['n' => $new->getBehavior('hit')->getHitsCount()]
                                ) ?>
                            </p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
<?php if($model->getSetting('showWrapper')):?>
    </div>
<?php endif; ?>
</div>
