<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use dosamigos\grid\EditableColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => 'Товар', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновить';
\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="product-update">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#product-product" data-toggle="tab">Карточка товара</a></li>
            <li><a href="#product-prices" data-toggle="tab">Цены</a></li>
            <li><a href="#product-stores" data-toggle="tab">Склады</a></li>
            <li><a href="#product-filters" data-toggle="tab">Фильтры</a></li>
            <li><a href="#product-fields" data-toggle="tab">Доп. поля</a></li>
        </ul>

        <div class="tab-content product-updater">
            <div class="tab-pane active" id="product-product">
                <?= $this->render('_form', [
                    'model' => $model
                ]) ?>
            </div>

            <div class="tab-pane" id="product-prices">
                <?php if ($dataProvider->getCount()) { ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel'  => $searchModel,
                        'columns'      => [
                            [
                                'attribute' => 'id',
                                'filter' => false,
                                'options' => ['style' => 'width: 25px;']
                            ],
                            [
                                'class'           => EditableColumn::className(),
                                'attribute'       => 'name',
                                'url'             => ['price/edit-field'],
                                'type'            => 'text',
                                'filter'          => false,
                                'editableOptions' => [
                                    'mode' => 'inline',
                                ],
                                'options'         => ['style' => 'width: 75px;'],
                            ],
                            [
                                'class'           => EditableColumn::className(),
                                'attribute'       => 'sort',
                                'url'             => ['price/edit-field'],
                                'type'            => 'text',
                                'editableOptions' => [
                                    'mode' => 'inline',
                                ],
                                'options'         => ['style' => 'width: 49px;'],
                            ],
                            [
                                'class'           => EditableColumn::className(),
                                'attribute'       => 'available',
                                'url'             => ['price/edit-field'],
                                'type'            => 'select',
                                'editableOptions' => [
                                    'mode'   => 'inline',
                                    'source' => ['yes', 'no'],
                                ],
                                'filter'          => false, /*Html::activeDropDownList(
                                $searchModel,
                                'available',
                                ['no' => 'Нет', 'yes' => 'Да'],
                                ['class' => 'form-control', 'prompt' => 'Наличие']
                            ),*/
                                'contentOptions'  => ['style' => 'width: 27px;'],
                            ],
                            [
                                'class'           => EditableColumn::className(),
                                'attribute'       => 'price',
                                'url'             => ['price/edit-field'],
                                'type'            => 'text',
                                'editableOptions' => [
                                    'mode' => 'inline',
                                ],
                                'options'         => ['style' => 'width: 40px;'],
                            ],
                            ['class' => 'yii\grid\ActionColumn', 'controller' => 'price', 'template' => '{delete}', 'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 30px;']],
                        ],
                    ]); ?>
                <?php } else { ?>
                    <p style="color: red;">У товара нет цен.</p>
                <?php } ?>
                <?= $this->render('price/_form', [
                    'model'        => $priceModel,
                    'productModel' => $model,
                ]) ?>
            </div>

            <div class="tab-pane" id="product-stores">
                <?php if ($StockDataProvider->getCount()) { ?>
                    <?= GridView::widget([
                        'dataProvider' => $StockDataProvider,
                        'filterModel'  => $StockSearch,
                        'columns'      => [
                            //['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width: 20px;']],
                            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 25px;']],
                            ['attribute' => 'name', 'filter' => false, 'options' => ['style' => 'width: 100px;']],
                            [
                                'class'           => EditableColumn::className(),
                                'attribute'       => $model->id,
                                'label'           => 'Количество',
                                'value'           => function ($data) use ($model) {
                                    return $data->getProductAmount($model->id);
                                },
                                'url'             => ['stock/edit-field'],
                                'type'            => 'text',
                                'editableOptions' => [
                                    'mode' => 'inline',
                                ],
                                'filter'          => false, /*Html::activeDropDownList(
                                $searchModel,
                                'available',
                                ['no' => 'Нет', 'yes' => 'Да'],
                                ['class' => 'form-control', 'prompt' => 'Наличие']
                            ),*/
                                'contentOptions'  => ['style' => 'width: 27px;'],
                            ],
                            ['class' => 'yii\grid\ActionColumn', 'controller' => 'price', 'template' => '', 'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 30px;']],
                        ],
                    ]); ?>
                <?php } ?>
            </div>

            <div class="tab-pane" id="product-filters">
                <?php if ($filterPanel = \app\modules\filter\widgets\Choice::widget(['model' => $model])) { ?>
                    <?= $filterPanel; ?>
                <?php } else { ?>
                    <p>В настоящий момент к категории данного товара не привязан ни один фильтр. Управлять фильтрами
                       можно <?= Html::a('здесь', ['/admin/filter/filter/index']); ?>.</p>
                <?php } ?>
            </div>


            <div class="tab-pane" id="product-fields">
                <?php if ($fieldPanel = \app\modules\field\widgets\Choice::widget(['model' => $model])) { ?>
                    <?= $fieldPanel; ?>
                <?php } else { ?>
                    <p>Поля не заданы. Задать можно <?= Html::a('здесь', ['/admin/field/field/index']); ?>.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
