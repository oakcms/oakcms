<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \app\modules\shop\models\category\CategorySearch $searchModel
 * @var string $route
 */

$this->title = Yii::t('shop', 'Select Category');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">
    <?php \yii\widgets\Pjax::begin(['id' => 'article-pjax']) ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
        'columns' => [
            [
                'attribute' => 'id',
                'options' => ['style' => 'width:100px']
            ],
            'title',
            'slug',
            [
                'header' => Yii::t('admin', 'Action'),
                'value' => function ($model) use ($route) {
                    /** @var $model \app\modules\content\models\ContentCategory */
                    return Html::a(Yii::t('admin', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('content', 'category: {title}', ['title' => $model->title]),
                            'route' => \app\modules\menu\models\MenuItem::toRoute($route, ['slug'=> $model->slug]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            //'value' => $model->category->slug . ':' . $model->slug
                        ]),
                    ]);
                },
                'options' => ['style' => 'width:80px'],
                'format' => 'raw'
            ]
        ]
	]) ?>
    <?php \yii\widgets\Pjax::end() ?>
</div>
