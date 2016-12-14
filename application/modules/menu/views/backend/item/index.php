<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\menu\models\MenuItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('menu', 'Menu Items');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('admin', 'Create'),
        'options' => [
            'href' => Url::to(['create'])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
    ]
];
?>
<div class="menu-index">

    <?= GridView::widget([
        'id'           => 'table-grid',
        'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            [
                'attribute' => 'id',
                'options'   => ['style' => 'width:36px'],
            ],
//            [
//                'attribute' => 'language',
//                'width'     => '80px',
//                'value'     => function ($model) {
//                    /** @var $model \app\modules\menu\models\MenuItem */
//                    //return \app\modules\main\widgets\TranslationsBackend::widget(['model' => $model]);
//                },
//                'format'    => 'raw',
//                //'filter' => Yii::$app->getAcceptedLanguagesList()
//            ],
            [
                'attribute' => 'menu_type_id',
                'options'   => ['style' => '100px'],
                'value'     => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return $model->menuType->title;
                },
                'filter'    => \yii\helpers\ArrayHelper::map(\app\modules\menu\models\MenuType::find()->all(), 'id', 'title'),
            ],
            [
                'attribute' => 'parent_id',
                'options'   => ['style' => '100px'],
                'value'     => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return $model->level > 2 ? $model->parent->title : '';
                },
                'filter'    => \yii\helpers\ArrayHelper::map(\app\modules\menu\models\MenuItem::find()->excludeRoots()->orderBy('lft')->all(), 'id', function (
                    $model
                ) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return str_repeat("- ", max($model->level - 2, 0)) . $model->title;
                }),
            ],
            [
                'attribute' => 'title',
                'value'     => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return str_repeat("&ndash;&nbsp; ", max($model->level - 2, 0)) . $model->title . '<br/>' . Html::tag('small', $model->path);
                },
                'format'    => 'raw',
            ],
            ['attribute' => 'link',],
            [
                'attribute' => 'status',
                'value'     => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return Html::beginTag('div', ['class' => 'btn-group']) .
                        Html::a('<i class="glyphicon glyphicon-star"></i>', \yii\helpers\Url::to(['status', 'id' => $model->id, 'status' => $model::STATUS_MAIN_PAGE]), ['class' => 'btn btn-xs' . ($model::STATUS_MAIN_PAGE == $model->status ? ' btn-success active' : ' btn-default'), 'data-pjax' => 0, 'data-method' => 'post', 'data-toggle' => 'tooltip', 'title' => Yii::t('menu', 'Main page')]) .
                        Html::a('<i class="glyphicon glyphicon-ok-circle"></i>', \yii\helpers\Url::to(['status', 'id' => $model->id, 'status' => $model::STATUS_PUBLISHED]), ['class' => 'btn btn-xs' . ($model::STATUS_PUBLISHED == $model->status ? ' btn-primary active' : ' btn-default'), 'data-pjax' => 0, 'data-method' => 'post', 'data-toggle' => 'tooltip', 'title' => Yii::t('menu', 'Status Published')]) .
                        Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', \yii\helpers\Url::to(['status', 'id' => $model->id, 'status' => $model::STATUS_UNPUBLISHED]), ['class' => 'btn btn-xs' . ($model::STATUS_UNPUBLISHED == $model->status ? ' btn-default active' : ' btn-default'), 'data-pjax' => 0, 'data-method' => 'post', 'data-toggle' => 'tooltip', 'title' => Yii::t('menu', 'Status Unpublished')]) .
                        Html::endTag('div');
                },
                'filter'    => \app\modules\menu\models\MenuItem::statusLabels(),
                'options'   => ['style' => '100px'],
                'format'    => 'raw',
            ],
//            [
//                'attribute' => 'ordering',
//                'value'     => function ($model) {
//                    /** @var $model \app\modules\menu\models\MenuItem */
//                    return Html::input('text', 'order', $model->ordering, ['class' => 'form-control']);
//                },
//                'format'    => 'raw',
//                'options'   => ['style' => '50px'],
//            ],
            [
                'class' => 'app\modules\admin\components\grid\ActionColumn',
                'translatable' => true
            ],
        ],
    ]); ?>

</div>

<script>
    function processOrdering(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows'),
            data = {}
        if (!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }
        $.each(selection, function (index, value) {
            data[value] = $grid.find('tr[data-key="' + value + '"] input[name="order"]').val()
        })

        $.post($el.attr('href'), {data: data}, function (response) {
            $grid.yiiGridView('applyFilter')
        })
    }
    function processAction(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows')
        if (!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }

        $.post($el.attr('href'), {data: selection}, function (response) {
            $grid.yiiGridView('applyFilter')
        })
    }
</script>
