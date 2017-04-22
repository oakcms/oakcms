<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.11.2016
 * Project: kardamon_blog
 * File name: _item.php
 *
 * @var $model \app\modules\content\models\ContentArticles
 */

use yii\helpers\Url;
use yii\bootstrap\Html;

?>
<a href="<?= Url::to(['/content/article/view', 'catslug' => $model->category->slug, 'slug' => $model->slug]) ?>" class="col-sm-6 cntnt__item wow slideInUpInUp">
    <div class="cntnt__item_img">
        <?php //= Html::img($model->getUploadUrl('image'), ['class' => 'img-thumbnail']) ?>
        <?= Html::img($model->getThumbUploadUrl('image'), ['class' => '', 'alt' => $model->title]) ?>
        <i class="fa fa-search-plus zoom-ico" aria-hidden="true"></i>
    </div>
    <div class="cntnt__item__dt-nm">
        <div class="cntnt__item__date">
            <div class="cntnt__item__date_dm">
                <span><?= date("d/m", $model->published_at) ?></span>
            </div>
            <div class="cntnt__item__date_year">
                <?= date("Y", $model->published_at) ?>
            </div>
        </div>
        <div class="cntnt__item__name">
            <div class="cntnt__item__name_ttl">
                <span><?= $model->title ?></span>
            </div>
            <div class="cntnt__item__name_rat-watch-com">
                <div class="col-xs-3 comm">
                    <span class="disqus-comment-count" data-disqus-url="<?= Url::to(['/content/article/view', 'catslug' => $model->category->slug, 'slug' => $model->slug], true) ?>"></span>
                    <i class="fa fa-commenting" aria-hidden="true"></i>
                </div>
                <div class="col-xs-3 eye">
                    <span><?= $model->getBehavior('hit')->getHitsCount() ?></span>
                    <span class="glyphicon glyphicon-eye-open"></span>
                </div>
                <div class="col-xs-6 star">
                    <?= \kartik\widgets\StarRating::widget([
                        'name' => 'rating_'.$model->id,
                        'value' => 2,
                        'pluginOptions' => [
                            'displayOnly' => true,
                            'emptyStar' => '<span class="glyphicon glyphicon-star"></span>',
                            'filledStar' => '<span class="glyphicon glyphicon-star active"></span>',
                            'showClear' => false,
                            'showCaption' => false,
                            'size' => 'small'
                        ]
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</a>
