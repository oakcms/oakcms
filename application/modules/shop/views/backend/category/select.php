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
    <?php \yii\widgets\Pjax::begin(['id' => 'categories']) ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'options' => ['style' => 'width:60px'],
            ],
            [
                'attribute' => 'name',
                'format' => 'raw'
            ],
//            [
//                'attribute' => 'status',
//                'value' => function ($model) {
//                    /** @var $model \app\modules\shop\models\Category */
//                    return $model->status;
//                },
//                'width' => '100px',
//            ],
            [
                'header' => Yii::t('admin', 'Action'),
                'value' => function ($model) use ($route) {
                    /** @var $model \app\modules\shop\models\Category */
                    return Html::a(Yii::t('admin', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->name,
                            'description' => Yii::t('shop', 'category: {title}', ['title' => $model->name]),
                            'route' => \app\modules\menu\models\MenuItem::toRoute($route, ['slug' => $model->slug]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->id . ':' . $model->slug
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
