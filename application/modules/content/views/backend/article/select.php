<?php

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
                'attribute' => 'category_id',
                'format' => 'raw',
                'filter' => Html::activeDropDownList($searchModel, 'category_id', \yii\helpers\ArrayHelper::map(
                    \app\modules\content\models\ContentCategory::find()->published()->all(),
                    'id',
                    'title'
                ), ['class'=>'form-control', 'prompt' => Yii::t('admin', 'Select Category')]),
                'value' => function($model, $index, $widget) {
                    return $model->category->title;
                }
            ],
            [
                'header' => Yii::t('admin', 'Action'),
                'value' => function ($model) use ($route) {
                    /** @var $model \app\modules\content\models\ContentArticles */
                    return Html::a(Yii::t('admin', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('shop', 'category: {title}', ['title' => $model->title]),
                            'route' => \app\modules\menu\models\MenuItem::toRoute($route, ['catslug'=> $model->category->slug, 'slug' => $model->slug]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->category->slug . ':' . $model->slug
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
