<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\menu\models\MenuTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('menu', 'Menu Types');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('content', 'Create'),
        'options' => [
            'href' => Url::to(['create'])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
    ],
];
?>
<div class="menu-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= GridView::widget([
        'id' => 'table-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'options' => ['style' => 'width:36px']
            ],
            [
                'attribute' => 'id',
                'options' => ['style' => 'width: 60px;']
            ],
            [
                'attribute' => 'title',
                'format' => 'raw'
            ],
            [
                'attribute' => 'alias',
            ],
            [
                'header' => Yii::t('menu', 'Menu Items'),
                'value' => function($model) {
                    /** @var $model \app\modules\menu\models\MenuType */
                    return Html::a('(' . $model->getItems()->count() . ')', ['/admin/menu/item/index', 'MenuItemSearch[menu_type_id]' => $model->id], ['data-pjax' => 0]);
                    },
                'format' => 'raw'
            ],
            [
                'class' => 'app\modules\admin\components\grid\ActionColumn',
            ],
        ],
    ]) ?>

</div>
