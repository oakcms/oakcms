<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\form_builder\models\FormBuilderForms;
use app\modules\form_builder\models\FormBuilderSubmission;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\form_builder\models\search\FormBuilderFormsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('form_builder', 'Form Builder Forms');
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
                [
                    'label' => '<span class="font-blue-chambray"><i class="fa fa-clone" aria-hidden="true"></i> ' . Yii::t('admin', 'Copy') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'copyA()']
                ],
            ],
        ],
    ]
];
?>
<div class="form-builder-forms-index">
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
                    'attribute' => 'title',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a($model->title, ['update', 'id' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'slug',
                    'format' => 'raw',
                    'value' => function($model) {
                        $url = ['/form_builder/form/view', 'slug' => $model->slug];
                        return Html::a(Url::to($url), $url, ['target' => '_blank']);
                    }
                ],
                [
                    'attribute' => 'submissions',
                    'header' => Yii::t('form_builder', 'Submissions'),
                    'format' => 'raw',
                    'value' => function($model) {
                        $count = FormBuilderSubmission::find()
                            ->where(['status' => FormBuilderSubmission::STATUS_DRAFT, 'form_id' => $model->id])
                            ->count();
                        return Html::a(
                            Html::tag(
                                'span',
                                Yii::t('form_builder', '{n} active submission', ['n' => $count]),
                                [
                                    'class' => 'label label-success'
                                ]
                            ),
                            ['/admin/form_builder/submissions', 'form_id' => $model->id]
                        );
                    }
                ],
                [
                    'attribute' => 'widget',
                    'format' => 'raw',
                    'value' => function($model) {
                        return '<code>[form_builder id="' . $model->id . '"]</code>';
                    },
                    'options' => ['style' => 'width:200px']
                ],
                [
                    'class'         => \app\modules\admin\components\grid\EnumColumn::className(),
                    'attribute'     => 'status',
                    'format'        => 'raw',
                    'options'       => ['width' => '50px'],
                    'value'         => function ($model, $index, $widget) {
                        return Html::checkbox('', $model->status == FormBuilderForms::STATUS_PUBLISHED, [
                            'class' => 'switch toggle',
                            'data-id' => $model->primaryKey,
                            'data-link' => \yii\helpers\Url::to(['/admin/form_builder/forms']),
                            'data-reload' => '0'
                        ]);
                    },
                    'enum' => [
                        Yii::t('admin', 'Off'),
                        Yii::t('admin', 'On')
                    ]
                ],

                ['class' => 'app\modules\admin\components\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
<script>
    function deleteA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['delete-ids']) ?>?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['published']) ?>?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['unpublished']) ?>?id=' + keys.join();
    }
    function copyA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Url::to(['clone-ids']) ?>?id=' + keys.join();
    }
</script>
