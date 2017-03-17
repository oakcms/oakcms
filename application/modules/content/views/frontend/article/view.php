<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

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
 * @var $this \yii\web\View;
 */

use yii\helpers\Url;
use yii\helpers\StringHelper;

$rating_id = 'rating_'.$model->id;

$meta_title = ($model->meta_title != '') ? $model->meta_title : $model->title;
$meta_description = StringHelper::truncate((($model->meta_description != '') ? $model->meta_description : strip_tags($model->description)), '140', '');
$meta_keywords = ($model->meta_keywords != '') ? $model->meta_keywords : implode(', ', explode(' ', $model->title));

$this->setSeoData($meta_title, $meta_description, $meta_keywords);

$this->registerJsFile('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js');
$this->registerJsFile('//yastatic.net/share2/share.js');
?>
<section class="article wow slideInUp" itemscope itemtype="http://schema.org/Article">
    <meta itemprop="datePublished" content="<?php date('Y-m-d', $model->published_at) ?>" />
    <div class="article__head clearfix">
        <h1 class="article__ttl col-xs-10" itemprop="headline"><?= $model->title ?></h1>
        <div class="cntnt__item__date col-xs-2">
            <div class="cntnt__item__date_dm">
                <span><?= date("d/m", $model->published_at) ?></span>
            </div>
            <div class="cntnt__item__date_year">
                <?= date("Y", $model->published_at) ?>
            </div>
        </div>
    </div>
    <div class="article__img">
        <img src="<?= $model->getUploadUrl('image') ?>" alt="<?= $model->title ?>">
    </div>
    <div class="article__txt">
        <div class="description">
            <?= $model->description ?>
        </div>
        <div class="content" itemprop="articleBody">
            <?= $model->content ?>
        </div>
    </div>
    <div class="article__footer clearfix">
        <div class="ya-share2" data-services="vkontakte,facebook,gplus,twitter,pocket" data-counter=""></div>
        <div class="star" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
            <meta itemprop="bestRating" content="5">
            <meta itemprop="ratingValue" content="<?= $model->rating ?>">
            <?= \kartik\widgets\StarRating::widget([
                'model' => $model,
                'attribute' => 'rating',
                'options' => ['id' => $rating_id],
                'pluginOptions' => [
                    'displayOnly' => Yii::$app->request->cookies->has('article_rating_'.$model->id),
                    'emptyStar' => '<span class="glyphicon glyphicon-star"></span>',
                    'filledStar' => '<span class="glyphicon glyphicon-star active"></span>',
                    'size' => 'small',
                    'showClear' => false,
                    'showCaption' => false,
                ],
                'pluginEvents' => [
                    'rating.change' => "function(event, value, caption) {
                        $.ajax({
                            type: 'POST',
                            url: '".Url::to()."',
                            data: {'rating': value},
                            cache: false,
                            success: function(data) {
                                var data = jQuery.parseJSON(data);
                                var inputRating = $('#".$rating_id."');
                                inputRating.rating('refresh', {disabled: true, showClear: false});
                                $('.numRait').text(data.rating);
                                $('.numVotes').text(data.ratingVotes);
                            }
                        });
                    }",
                ],
            ]); ?>
            <span class="star__dsc">(<span class="numRait"><?= $model->rating ?></span> из  5 всего <span class="numVotes" itemprop="ratingCount"><?= $model->rating_votes ?></span> оценок)</span>
        </div>
        <div class="comm-eye">
            <div class="comm">
                <span class="disqus-comment-count" data-disqus-url="<?= Url::to('', true) ?>"></span>
                <i class="fa fa-commenting" aria-hidden="true"></i>
            </div>
            <div class="eye">
                <span><?= $model->getBehavior('hit')->getHitsCount() ?></span>
                <span class="glyphicon glyphicon-eye-open"></span>
            </div>
        </div>
    </div>
    <div class="article__tags">
        <div class="article__tags_ttl">
            <span>
                Еще статьи по теме:
            </span>
        </div>
        <div class="cntnt__tags__text">
            <?php foreach (explode(',', $model->tagNames) as $tagName):?>
                <a href="<?= Url::to(['/content/article/tag', 'tag' => trim($tagName)]) ?>" class="cntnt__tags__text_tag">
                    <?= trim($tagName) ?>
                </a>
            <?php endforeach;?>
        </div>
    </div>
    <div class="article__discus">
        <div id="disqus_thread"></div>
    </div>
</section>
