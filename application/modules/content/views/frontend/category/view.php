<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\content\models\ContentCategory;
 * @var $dataProvider \yii\data\ActiveDataProvider;
 * @var $breadcrumbs array
 */

use yii\widgets\ListView;


$this->params['breadcrumbs'] = $breadcrumbs;

$this->setSeoData(($model->meta_title != '')?$model->meta_title:$model->title, $model->meta_description, $model->meta_keywords);

?>
<h1><?= $model->title ?></h1>
<section class="main-news-container">
    <div class="main-news">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'main-news-article__row'],
            'itemView' => '_item',
            'layout' => "{items}\n<div class='container'><div class='text-center relative'>{pager}</div></div>",
            'pager' => [
                'firstPageLabel' => Yii::t('content', 'First page'),
                'lastPageLabel' => Yii::t('content', 'Last page'),
            ]
        ]); ?>
    </div>
</section>
