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
<div class="content-pages-index">

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
                'title_h1',
                'title',
                'layout',
                'status',
                'created_at:datetime',
                'updated_at:datetime',
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
