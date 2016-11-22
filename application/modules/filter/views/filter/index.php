<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Фильтры';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filter-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить фильтр', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
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
