<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\field\models\Category;

$this->title = 'Поля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить поле', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Категории', ['/field/category'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'export' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'slug',
            [
                'attribute' => 'category_id',
                'label' => 'Категория',
                'content' => function($model) {
                    if($model->category) {
                        return $model->category->name;
                    }
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'type',
                    ArrayHelper::map(Category::find()->all(), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'Категория']
                )
            ],
            [
                'attribute' => 'relation_model',
                'content' => function($model) {
                    return @yii::$app->getModule('field')->relationModels[$model->relation_model];
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'type',
                    yii::$app->getModule('field')->relationModels,
                    ['class' => 'form-control', 'prompt' => 'Модель']
                )
                ],
                [
                    'attribute' => 'type',
                    'content' => function($model) {
                    return yii::$app->getModule('field')->types[$model->type];
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'type',
                    yii::$app->getModule('field')->types,
                    ['class' => 'form-control', 'prompt' => 'Тип']
                )
            ],
            'description',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 110px;']],
        ],
    ]); ?>

</div>
