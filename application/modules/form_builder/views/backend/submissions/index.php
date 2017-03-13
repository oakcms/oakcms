<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\form_builder\models\search\FormBuilderFormsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('form_builder', 'Form Builder Submissions');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
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

$columns = [
    [
        'class' => 'yii\grid\CheckboxColumn',
        'options' => ['style' => 'width:36px']
    ],
    'id',
    'ip',
    [
        'attribute' => 'created',
        'format' => 'datetime'
    ],
    'status'
];

$columns = array_merge($columns, array_map('strval', array_keys($formModel->fieldsAttributes)));
$columns = array_merge($columns, [['class' => 'app\modules\admin\components\grid\ActionColumn']]);

?>
<div class="form-builder-forms-index">
    <div class="table-responsive">
        <?= GridView::widget([
            'id' => 'grid',
            //'showHeader' => false,
            'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
            'dataProvider' => $dataProvider,
            'columns' => $columns,
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
</script>
