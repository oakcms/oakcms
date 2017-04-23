<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\menu\models\MenuTypeSearch $searchModel
 */

$this->title = Yii::t('menu', 'Select Menu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'id' => 'grid',
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
		'columns' => [
            [
                'attribute' => 'id',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px'
            ],
            [
                'attribute' => 'title',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model) {
                        /** @var $model \app\modules\menu\models\MenuType */
                        return $model->title . '<br/>' . Html::tag('small', $model->alias, ['class' => 'text-muted']);
                    },
                'format' => 'html'
            ],
            [
                'header'      => Yii::t('menu', 'Action'),
                'hAlign'      => GridView::ALIGN_CENTER,
                'vAlign'      => GridView::ALIGN_MIDDLE,
                'value'       => function($model) {
                    return Html::a(Yii::t('menu', 'Select'), '#', [
                        'class'   => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id'          => $model->id,
                            'description' => Yii::t('menu', 'Menu Type: {title}', ['title' => $model->title]),
                            'value'       => $model->id . ':' . $model->alias
                            ]),
                    ]);
                },
                'width'       => '80px',
                'mergeHeader' => true,
                'format'      =>'raw'
            ]
		],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 0],
        'bordered' => false,
        'panel' => [
            'heading'    => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type'       => 'info',
            'after'      => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('menu', 'Reset List'), [null], ['class' => 'btn btn-info']),
            'showFooter' => false,
        ],
	]) ?>
</div>
