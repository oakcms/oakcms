<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\language\models\LanguageSource;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\language\models\search\LanguageSourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('language', 'Language Sources');
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
<div class="language-source-index">

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
                'category',
                'message:ntext',
                ['class' => 'app\modules\admin\components\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
<script>
    function deleteA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/seo/delete-ids?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/seo/published?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '/admin/seo/unpublished?id=' + keys.join();
    }
</script>
