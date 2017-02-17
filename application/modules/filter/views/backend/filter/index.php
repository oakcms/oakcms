<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;

$this->title = Yii::t('filter', 'Filters');
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
    ]
];

?>
<div class="filter-index">
    <?= GridView::widget([
        'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'slug',
            [
                'attribute' => 'category',
                'label' => 'Категория',
                'content' => function($model) {
                    $return = [];
                    foreach($model->relation_field_value as $category) {
                        $fieldValues = Yii::$app->getModule('filter')->relationFieldValues;
                        if(isset($fieldValues[$category])) {
                            $return[] = $fieldValues[$category];
                        }
                    }

                    return implode(', ', $return);
                },
                'filter' => false,
            ],
            [
                'attribute' => 'is_filter',
                'content' => function($model) {
                    if($model->is_filter == 'yes') {
                        return 'Да';
                    } else {
                        return 'Нет';
                    }
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'is_filter',
                    ['no' => 'Нет', 'yes' => 'Да'],
                    ['class' => 'form-control', 'prompt' => 'Фильтр']
                )
            ],
            [
                'attribute' => 'type',
                'content' => function($model) {
                    if($model->type == 'checkbox') {
                        return 'Много вариантов';
                    } elseif($model->type == 'radio') {
                        return 'Один вариант';
                    }
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'type',
                    yii::$app->getModule('filter')->types,
                    ['class' => 'form-control', 'prompt' => 'Тип']
                )
            ],
            'description',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 110px;']],
        ],
    ]); ?>

</div>
