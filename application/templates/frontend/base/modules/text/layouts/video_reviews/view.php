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


use yii\helpers\ArrayHelper;
use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentArticlesLang;

$articles = ContentArticles::find()
    ->joinWith(['translations'])
    ->where([
        ContentArticles::tableName().'.id' => $model->getSetting('items'),
        ContentArticles::tableName().'.status' => ContentArticles::STATUS_PUBLISHED,
        ContentArticlesLang::tableName().'.language' => Yii::$app->language
    ])
    ->all();

$articles = ArrayHelper::toArray(
    $articles,
    [
        'app\modules\content\models\ContentArticles' => [
//            'id',
            'title',
            'content',
//            'link' => function ($post) {
//                return $post->getFrontendViewLink();
//            },
        ],
    ]
);

$articles = array_chunk($articles, 2);
if(count($articles) > 0) :
?>
    <div class="block_feedback_slider_sidebar">
        <h3><?= $model->getSetting('title') ?></h3>
        <div class="list_feedback">
            <div class="owl-carousel inline-layout">
                <?php foreach ($articles as $items): ?>
                <div class="item-slider">
                    <?php foreach ($items as $article):?>
                    <div class="item">
                        <div class="block_video">
                            <?= ArrayHelper::getValue($article, 'content', '') ?>
                        </div>
                        <div class="name"><?= ArrayHelper::getValue($article, 'title', '') ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
