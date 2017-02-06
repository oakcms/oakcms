<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\content\models\ContentPages;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\content\models\search\ContentPagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('content', 'Content Pages');
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
    [
        'label' => Yii::t('admin', 'Control'),
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
                    'label' => '<span class="font-red"><i class="fa fa-trash-o"></i> ' . Yii::t('admin', 'Delete') . '</span>',
                    'url' => 'javascript:void(0)',
                        'linkOptions' => [
                        'onclick' => 'deleteA()',
                    ]
                ],
                [
                    'label' => '<span class="font-green-turquoise"><i class="fa fa-toggle-on"></i> ' . Yii::t('admin', 'Published') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'publishedA()']
                ],
                [
                    'label' => '<span class="font-blue-chambray"><i class="fa fa-toggle-off"></i> ' . Yii::t('admin', 'Unpublished') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'unpublishedA()']
                ],
            ],
        ],
    ]
];
?>
<div class="content-pages-index">

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'id' => 'grid',
            'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
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
            'columns' => [
                [
                    'class' => \kotchuprik\sortable\grid\Column::className(),
                ],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'options' => ['style' => 'width:36px']
                ],
                [
                    'attribute' => 'id',
                    'options' => ['style' => 'width:100px']
                ],
                [
                    'attribute' => 'title',
                    'value'     => function ($model) {
                        /** @var $model \app\modules\content\models\ContentPages */
                        return str_repeat("&ndash;&nbsp; ", max($model->level - 1, 0)) . $model->title;
                    },
                    'format'    => 'raw',
                ],
                'slug',
                [
                    'attribute' => 'layout',
                    'value'     => function ($model) {
                        /** @var $model \app\modules\content\models\ContentPages */
                        return \yii\helpers\Inflector::camel2words($model->layout);
                    },
                    'filter'    => \yii\helpers\ArrayHelper::map(ContentPages::find()->excludeRoots()->select(['layout'])->orderBy('lft')->all(), 'layout', function (
                        $model
                    ) {
                        /** @var $model \app\modules\content\models\ContentPages */
                        return \yii\helpers\Inflector::camel2words($model->layout);
                    }),
                ],
                [
                    'class' => \app\modules\admin\components\grid\EnumColumn::className(),
                    'attribute' => 'status',
                    'format' => 'raw',
                    'options' => ['width' => '50px'],
                    'value' => function ($model, $index, $widget) {
                        return Html::checkbox('', $model->status == ContentPages::STATUS_PUBLISHED, [
                            'class' => 'switch toggle',
                            'data-id' => $model->primaryKey,
                            'data-link' => \yii\helpers\Url::to(['/admin/content/pages']),
                            'data-reload' => '0'
                        ]);
                    },
                    'enum' => [
                        Yii::t('admin', 'Off'),
                        Yii::t('admin', 'On')
                    ]
                ],
                [
                    'class' => 'app\modules\admin\components\grid\ActionColumn',
                    'translatable' => true
                ],
            ],
        ]); ?>
    </div>
</div>
<script>
    function deleteA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['/admin/content/pages/delete-ids']) ?>' + '?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['/admin/content/pages/delete-ids']) ?>' + '?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['/admin/content/pages/delete-ids']) ?>' + '?id=' + keys.join();
    }
</script>
