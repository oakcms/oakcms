<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\content\models\ContentArticles;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\content\models\search\ContentArticlesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Content Articles');
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
<div class="content-articles-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
        <?= GridView::widget([
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
                    'attribute' => 'id',
                    'options' => ['style' => 'width:100px']
                ],
                'title',
                'slug',
                'published_at:datetime',
                'created_at:datetime',
                [
                    'class' => \app\modules\admin\components\grid\EnumColumn::className(),
                    'attribute' => 'status',
                    'format' => 'raw',
                    'options' => ['width' => '50px'],
                    'value' => function ($model, $index, $widget) {
                        return Html::checkbox('', $model->status == ContentArticles::STATUS_PUBLISHED, [
                            'class' => 'switch toggle',
                            'data-id' => $model->primaryKey,
                            'data-link' => \yii\helpers\Url::to(['/admin/content/article']),
                            'data-reload' => '0'
                        ]);
                    },
                    'enum' => [
                        Yii::t('app', 'Off'),
                        Yii::t('app', 'On')
                    ]
                ],
                // 'comment_status',
                // 'create_user_ip',
                // 'access_type',
                // 'category_id',
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
        window.location.href = '/admin/content/article/delete-ids?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/content/article/published?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/content/article/unpublished?id=' + keys.join();
    }
</script>
