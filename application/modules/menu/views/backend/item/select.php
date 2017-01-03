<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\menu\models\MenuItemSearch $searchModel
 */

$this->title = Yii::t('menu', 'Select Menu Item');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'id' => 'grid',
        'columns' => [
            [
                'attribute' => 'id',
                'options' => ['width' => '60px']
            ],
            [
                'attribute' => 'language',
                'options' => ['width' => '60px'],
                'filter' => \app\modules\language\models\Language::getAllLang()
            ],
            [
                'attribute' => 'menu_type_id',
                'options' => ['width' => '120px'],
                'value' => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return $model->menuType->title;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\app\modules\menu\models\MenuType::find()->all(), 'id', 'title')
            ],
            [
                'attribute' => 'title',
                'value' => function ($model) {
                        /** @var $model \app\modules\menu\models\MenuItem */
                        return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', $model->path, ['class' => 'text-muted']);
                    },
                'format' => 'raw'

            ],
			[
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return $model->getStatusLabel();
                },
                'options' => ['width' => '150px'],
                'filter' => \app\modules\menu\models\MenuItem::statusLabels()
            ],
            [
                'header' => Yii::t('menu', 'Action'),
                'value' => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return Html::a(Yii::t('menu', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('menu', 'Menu Item: {title}', ['title' => $model->title]),
                            'route' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->id . ':' . $model->alias
                        ]),
                    ]);
                },
                'options' => ['width' => '80px'],
                'format' => 'raw'
            ]
        ],
	]) ?>

</div>
