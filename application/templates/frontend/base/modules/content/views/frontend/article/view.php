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
use yii\helpers\Url;

$this->params['breadcrumbs'] = $breadcrumbs;

$this->setSeoData(($model->meta_title != '')?$model->meta_title:$model->title, $model->meta_description, $model->meta_keywords);
?>
<div id="novelty">
    <div class="news">
        <div class="container">
            <h2><?= $categoryModel->title ?></h2>
            <div class="row">
                <div class="container-fluid">
                    <div class="col-sm-4">
                        <div class="news-photo">
                            <a href="<?= Url::to(['/content/article/view', 'catslug' => $categoryModel->slug, 'slug'=>$model->slug]) ?>">
                                <img src="<?= $model->getUploadUrl('image') ?>" alt="<?= $model->title ?>">
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-7">
                        <h3>
                            <?= Html::a($model->title, ['/content/article/view', 'catslug' => $model->category->slug, 'slug'=>$model->slug]); ?>
                        </h3>

                        <?= $model->description ?>
                        <?= $model->content ?>
                        <span class="date"><?= Yii::$app->formatter->asDate($model->published_at, 'long') ?></span>

                        <a href="<?= Url::to(['/content/category/view', 'slug' => $categoryModel->slug]) ?>" class="btn">К новостям</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


