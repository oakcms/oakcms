<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \app\modules\content\models\search\ContentPagesSearch $searchModel
 * @var string $route
 */


$this->title = Yii::t('content', 'Select Page');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'grid',
        'columns' => [
            [
                'attribute' => 'id',
                'options' => ['width' => '60px'],
            ],
            //[
            //    'attribute' => 'title',
//                'value' => function($model){
//                    /** @var \app\modules\content\models\ContentPages $model */
//                    return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', ' — ' . $model->path, ['class' => 'text-muted']);
//                },
//                'format' => 'raw'
//            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model \app\modules\content\models\ContentPages */
                    return $model->getStatusLabel();
                },
                'options' => ['width' => '100px'],
                'filter' => \app\modules\content\models\ContentPages::statusLabels()
            ],
            [
                'header' => Yii::t('menu', 'Action'),
                'value' => function ($model) use ($route) {
                    /** @var $model \app\modules\content\models\ContentPages */
                    return Html::a(Yii::t('menu', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \app\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('menu', 'Page: {title}', ['title' => $model->title]),
                            'route' => \app\modules\menu\models\MenuItem::toRoute($route, ['slug' => $model->slug]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->id . ':' . $model->slug
                        ]),
                    ]);
                },
                'width' => '80px',
                'mergeHeader' => true,
                'format' => 'raw'
            ]
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 0],
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . '</h3>',
            'type' => 'info',
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), [null], ['class' => 'btn btn-info']),
            'showFooter' => false,
        ],
    ]) ?>

</div>
