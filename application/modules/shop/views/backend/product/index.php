<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use app\modules\shop\models\Category;
use app\modules\shop\models\Price;
use app\modules\shop\models\Producer;
use app\modules\shop\models\ProductOption;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;

\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="product-index">
    <div class="shop-menu">
        <?=$this->render('../parts/menu');?>
    </div>

    <div class="row">
        <div class="col-md-2">
            <?= Html::a('Добавить товар', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-md-2">
            <?php
            $gridColumns = [
                'id',
                'code',
                'category.name',
                'producer.name',
                'name',
                'price',
                'amount',
            ];

            echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns
            ]);
            ?>
        </div>
    </div>

    <?php if ($amount = $dataProvider->query->sum('amount')) { ?>
    <div class="summary">
            Всего остатков: Всего товаров:
            <?= $amount; ?>
        </div>
        на сумму
        <?= Price::find()->joinWith('product')->sum("shop_price.price*shop_product.amount"); ?>
        руб.
    </div>
    <?php } ?>

    <br style="clear: both;"></div>
    <?php
    echo \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 55px;']],
            'name',
            'code',
            [
                'label' => 'Остаток',
                'content' => function ($model) {
                    return "<p>{$model->amount} (" . ($model->amount * $model->price) . ")</p>";
                }
            ],
            [
                'attribute' => 'images',
                'format' => 'images',
                'filter' => false,
                'content' => function ($image) {
                    if($image = $image->getImage()->getUrl('50x50')) {
                        return "<img src=\"{$image}\" class=\"thumb\" />";
                    }
                }
            ],
            [
                'label' => 'Цена',
                'value' => 'price'
            ],
            /*
            [
                'attribute' => 'available',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'available',
                    ['no' => 'Нет', 'yes' => 'Да'],
                    ['class' => 'form-control', 'prompt' => 'Наличие']
                ),
            ],
            */
            [
                'attribute' => 'category_id',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'category_id',
                    Category::buildTextTree(),
                    ['class' => 'form-control', 'prompt' => 'Категория']
                ),
                'value' => 'category.name'
            ],
            [
                'attribute' => 'producer_id',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'producer_id',
                    ArrayHelper::map(Producer::find()->all(), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'Производитель']
                ),
                'value' => 'producer.name'
            ],

            [
                'class' => 'app\modules\admin\components\grid\ActionColumn',
                'translatable' => true
            ],
        ],
    ]); ?>

</div>
