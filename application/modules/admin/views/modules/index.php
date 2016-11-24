<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\admin\models\ModulesModules;
use yii\jui\JuiAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\search\ModulesModulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Modules Modules');
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
    ],
    [
        'label' => Yii::t('app', 'Control'),
        'options' => [
            'class' => 'btn blue btn-outline btn-circle btn-sm',
            'data-hover' => "dropdown",
            'data-close-others' => "true",
        ],
        'dropdown' => [
            'options' => ['class' => 'pull-right'],
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => '<span class="font-red"><i class="fa fa-trash-o"></i> ' . Yii::t('app', 'Delete') . '</span>',
                    'url' => 'javascript:void(0)',
                        'linkOptions' => [
                        'onclick' => 'deleteA()',
                    ]
                ],
                [
                    'label' => '<span class="font-green-turquoise"><i class="fa fa-toggle-on"></i> ' . Yii::t('app', 'Published') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'publishedA()']
                ],
                [
                    'label' => '<span class="font-blue-chambray"><i class="fa fa-toggle-off"></i> ' . Yii::t('app', 'Unpublished') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'unpublishedA()']
                ],
            ],
        ],
    ]
];
?>
<div class="modules-modules-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
        <?= \himiklab\sortablegrid\SortableGridView::widget([
            'id' => 'grid',
            'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'options' => ['style' => 'width:36px']
                ],
                [
                    'attribute' => 'module_id',
                    'options' => ['style' => 'width:100px']
                ],
                'name',
                'title',
                'class',
                //'isFrontend',
                //'controllerNamespace',
                // 'viewPath',
                // 'isAdmin',
                // 'BackendControllerNamespace',
                // 'AdminViewPath',
                // 'icon',
                // 'settings:ntext',
                // 'order',
                [
                    'class' => \app\modules\admin\components\grid\EnumColumn::className(),
                    'attribute' => 'status',
                    'format' => 'raw',
                    'options' => ['width' => '50px'],
                    'value' => function ($model, $index, $widget) {
                        return Html::checkbox('', $model->status == ModulesModules::STATUS_PUBLISHED, [
                            'class' => 'switch toggle',
                            'data-id' => $model->primaryKey,
                            'data-link' => \yii\helpers\Url::to(['/admin/modules']),
                            'data-reload' => '1'
                        ]);
                    },
                    'enum' => [
                        Yii::t('app', 'Off'),
                        Yii::t('app', 'On')
                    ]
                ],
                [
                    'class' => 'app\modules\admin\components\grid\ActionColumn',
                    'template'=>'<div class="btn-group">{setting} {update} {delete}</div>',
                    'buttons' => [
                        'setting' => function($url, $model) {
                            $options = [
                                'title' => \Yii::t('app', 'Settings'),
                                'class'=>'btn blue-hoki btn-xs',
                                //'style' => 'margin-right:0',
                                'data-toggle' => 'tooltip',
                                'data-pjax' => '0',
                            ];
                            return Html::a('<i class="fa fa-cog"></i>', ['setting', 'name' => $model->name], $options);
                        }
                    ]
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
