<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\models\Modules;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\search\ModulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $system_modules array */

$this->title = Yii::t('admin', 'Modules Modules');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName'      => 'a',
        'label'        => Yii::t('admin', 'Create'),
        'options'      => [
            'href' => Url::to(['create']),
        ],
        'icon'         => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size'         => Button::SIZE_SMALL,
        'disabled'     => false,
        'block'        => false,
        'type'         => Button::TYPE_CIRCLE,
    ],
    [
        'label'    => Yii::t('admin', 'Control'),
        'options'  => [
            'class'             => 'btn blue btn-outline btn-circle btn-sm',
            'data-hover'        => "dropdown",
            'data-close-others' => "true",
        ],
        'dropdown' => [
            'options'      => ['class' => 'pull-right'],
            'encodeLabels' => false,
            'items'        => [
                [
                    'label'       => '<span class="font-red"><i class="fa fa-trash-o"></i> ' . Yii::t('admin', 'Delete') . '</span>',
                    'url'         => 'javascript:void(0)',
                    'linkOptions' => [
                        'onclick' => 'deleteA()',
                    ],
                ],
                [
                    'label'       => '<span class="font-green-turquoise"><i class="fa fa-toggle-on"></i> ' . Yii::t('admin', 'Published') . '</span>',
                    'url'         => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'publishedA()'],
                ],
                [
                    'label'       => '<span class="font-blue-chambray"><i class="fa fa-toggle-off"></i> ' . Yii::t('admin', 'Unpublished') . '</span>',
                    'url'         => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'unpublishedA()'],
                ],
            ],
        ],
    ],
];
?>
<div class="modules-modules-index">
    <div class="table-responsive">
        <?= \yii\grid\GridView::widget([
            'id'           => 'grid',
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
            'options'      => [
                'class' => 'grid-view',
                'data'  => [
                    'sortable-widget' => 1,
                    'sortable-url'    => \yii\helpers\Url::toRoute(['sorting']),
                ],
            ],
            'rowOptions'   => function ($model) {
                return ['data-sortable-id' => $model->module_id];
            },
            'columns'      => [
                [
                    'class' => \kotchuprik\sortable\grid\Column::className(),
                ],
                [
                    'class'   => 'yii\grid\CheckboxColumn',
                    'options' => ['style' => 'width:36px'],
                ],
                [
                    'attribute' => 'module_id',
                    'options'   => ['style' => 'width:100px'],
                ],
                'name',
                'title',
                'class',
                [
                    'class'     => \app\modules\admin\components\grid\EnumColumn::className(),
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'options'   => ['width' => '50px'],
                    'value'     => function ($model) use ($system_modules) {
                        /** @var $model Modules */
                        if (in_array($model->name, $system_modules)) {
                            return '<div class="text-center"><i class="fa fa-shield fa-lg" style="color:#00c0ef" aria-hidden="true"></i></div>';
                        }

                        return Html::checkbox('', $model->status == Modules::STATUS_PUBLISHED, [
                            'class'       => 'switch toggle',
                            'data-id'     => $model->primaryKey,
                            'data-link'   => \yii\helpers\Url::to(['/admin/modules']),
                            'data-reload' => '1',
                        ]);
                    },
                    'enum'      => [
                        Yii::t('admin', 'Off'),
                        Yii::t('admin', 'On'),
                    ],
                ],
                [
                    'class'    => 'app\modules\admin\components\grid\ActionColumn',
                    'template' => '<div class="btn-group">{setting} {update} {delete}</div>',
                    'buttons'  => [
                        'setting' => function ($url, $model) use ($system_modules) {
                            $options = [
                                'title' => \Yii::t('admin', 'Settings'),
                                'class' => 'btn blue-hoki btn-xs',
                                'data'  => [
                                    'toggle' => 'tooltip',
                                    'pjax'   => 0,
                                ],
                            ];

                            return Html::a('<i class="fa fa-cog"></i>', ['setting', 'name' => $model->name], $options);
                        },
                        'update'  => function ($url, $model) use ($system_modules) {
                            $options = [
                                'title' => \Yii::t('admin', 'Edit'),
                                'class' => 'btn btn-xs green' . (in_array($model->name, $system_modules) ? ' disabled' : ''),
                                'data'  => [
                                    'toggle' => 'tooltip',
                                    'pjax'   => 0,
                                ],
                            ];

                            return Html::a('<span class="fa fa-edit"></span>', $url, $options);
                        },
                        'delete'  => function ($url, $model) use ($system_modules) {
                            $options = [
                                'title' => \Yii::t('admin', 'Delete'),
                                'class' => 'btn red btn-xs' . (in_array($model->name, $system_modules) ? ' disabled' : ''),
                                'data'  => [
                                    'toggle'  => 'tooltip',
                                    'pjax'    => 0,
                                    'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                                ],
                            ];

                            return Html::a('<span class="fa fa-trash-o"></span>', $url, $options);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
<script>
    function deleteA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/modules/delete-ids?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/modules/published?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/modules/unpublished?id=' + keys.join();
    }
</script>
