<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use app\modules\admin\widgets\Button;
use app\modules\menu\models\MenuItem;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\icons\Icon;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\menu\models\MenuItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Icon::map($this, Icon::FI);

$this->title = Yii::t('menu', 'Menu Items');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName'      => 'a',
        'label'        => Yii::t('admin', 'Create'),
        'options'      => [
            'href' => Url::to(['create', 'menuTypeId' => $searchModel->menu_type_id]),
        ],
        'icon'         => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size'         => Button::SIZE_SMALL,
        'disabled'     => false,
        'block'        => false,
        'type'         => Button::TYPE_CIRCLE,
    ],
];
?>
<div class="menu-index">
    <?= GridView::widget([
        'id'           => 'table-grid',
        'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'options' => [
            'class' => 'grid-view',
            'data' => [
                'sortable-widget' => 1,
                'sortable-url' => \yii\helpers\Url::toRoute(['sorting']),
            ],
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['data-sortable-id' => $model->id];
        },
        'columns'      => [
            [
                'class' => \kotchuprik\sortable\grid\Column::className(),
            ],
            [
                'attribute' => 'id',
                'options'   => ['style' => 'width:36px'],
            ],
            [
                'attribute' => 'language',
                'options'   => ['style' => 'width:80px'],
                'value' => function ($model) {
                    /** @var $model MenuItem */
                    return \app\modules\menu\widgets\TranslationsBackend::widget(['model' => $model]);
                },
                'format' => 'raw',
                'filter' => \app\modules\language\models\Language::getAllLang()
            ],
            [
                'attribute' => 'title',
                'value'     => function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return str_repeat("&ndash;&nbsp; ", max($model->level - 2, 0)) . $model->title . '<br/>' . Html::tag('small', $model->path);
                },
                'format'    => 'raw',
            ],
            [
                'attribute' => 'menu_type_id',
                'options'   => ['style' => 'width: 100px'],
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
                'filter'    => \yii\helpers\ArrayHelper::map(\app\modules\menu\models\MenuItem::find()->excludeRoots()->orderBy('lft')->all(), 'id', function ($model) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return str_repeat("- ", max($model->level - 2, 0)) . $model->title;
                }),
            ],
            ['attribute' => 'link'],
            [
                'attribute' => 'main',
                'format'    => 'raw',
                'options'   => ['width' => '50px'],
                'header'    => Yii::t('menu', 'Home'),
                'filter'    => Html::activeDropDownList(
                    $searchModel,
                    'main',
                    [MenuItem::STATUS_MAIN_PAGE => Yii::t('menu', 'Yes')],
                    ['class' => 'form-control', 'prompt' => Yii::t('menu', 'Select')]
                ),
                'value'     => function ($model) {
                    /** @var $model MenuItem */
                    $class = ['fa', 'fa-home', 'switch-fa'];
                    if ($model->status == MenuItem::STATUS_MAIN_PAGE) $class[] = 'switch-fa--active';

                    return Html::a(
                        Html::tag('span', '', ['class' => $class]),
                        [
                            '/admin/menu/item/status',
                            'id' => $model->primaryKey,
                            'status' => MenuItem::STATUS_MAIN_PAGE
                        ],
                        [
                            'data'  => [
                                'method' => 'post',
                                'toggle' => 'tooltip',
                                'original-title' => Yii::t('menu', 'Set home page')
                            ]
                        ]
                    );
                },
            ],
            [
                'class'     => \app\modules\admin\components\grid\EnumColumn::className(),
                'attribute' => 'status',
                'format'    => 'raw',
                'options'   => ['width' => '50px'],
                'value'     => function ($model, $index, $widget) {
                    return Html::checkbox('', $model->status == MenuItem::STATUS_PUBLISHED || $model->status == MenuItem::STATUS_MAIN_PAGE, [
                        'class'       => 'switch toggle ',
                        'data-id'     => $model->primaryKey,
                        'data-link'   => \yii\helpers\Url::to(['/admin/menu/item']),
                        'data-reload' => '0',
                        'disabled'    => $model->status == MenuItem::STATUS_MAIN_PAGE,
                    ]);
                },
                'enum'      => [
                    Yii::t('admin', 'Off'),
                    Yii::t('admin', 'On'),
                ],
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
                'class'        => 'app\modules\admin\components\grid\ActionColumn'
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
