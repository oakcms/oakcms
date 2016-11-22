<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.10.2016
 * Project: kotsyubynsk
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
<section class="news" style="margin-bottom: 20px">
    <h2><?= $model->title ?></h2>
    <div class="container">
        <div class="rows">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'news-wrapp'],
                'itemView' => '_item',
                'layout' => "{items}\n<div class='container'><div class='text-center relative'>{pager}</div></div>",
                'pager' => [
                    'prevPageLabel' => false,
                    'nextPageLabel' => false,
                    'firstPageLabel' => false,
                    'lastPageLabel' => false,
                    'options' => [
                        'class' => 'nav-links text-center'
                    ]
                ]
            ]); ?>
        </div>
    </div>
</section>
