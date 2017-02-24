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


//$this->params['breadcrumbs'] = $breadcrumbs;

$this->setSeoData(($model->meta_title != '')?$model->meta_title:$model->title, $model->meta_description, $model->meta_keywords);
?>
<section class="thumbnav">
    <h2><?= $model->title ?></h2>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'news-wrapp'],
        'itemView' => '../article/_item',
        'layout' => "<div class='row'>{items}</div>\n<div><div class='text-center relative'>{pager}</div></div>",
        'pager' => [
            'options' => [
                'class' => 'pagination'
            ]
        ]
    ]); ?>
</section>

