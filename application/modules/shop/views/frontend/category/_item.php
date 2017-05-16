<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 30.12.2016
 * Project: oakcms
 * File name: _item.php
 *
 * @var $model \app\modules\shop\models\Product;
 */

use yii\helpers\Html;

?>
<div class="col-xs-12 col-sm-6 col-md-4">
    <article class="product-cut">
        <div class="product-cut__main-info">
            <div class="product-cut__photo">
                <div class="product-photo">
                    <div class="product-photo__item">
                        <?php
                        if(count($model->modifications)) {
                            $img = Html::img($model->modifications[0]->getImage()->getUrl('387x285'), [
                                'class' => 'product-photo__img',
                                'alt' => $model->name,
                                'title' => $model->name,
                                'data-product-photo' => true,
                            ]);
                        } else {
                            $img = Html::img($model->getImage()->getUrl('387x285'), [
                                'class' => 'product-photo__img',
                                'alt' => $model->name,
                                'title' => $model->name,
                                'data-product-photo' => true,
                            ]);
                        }
                        ?>
                        <?= Html::a(
                            $img,
                            ['/shop/product/view', 'slug' => $model->slug]
                        ) ?>
                    </div>
                </div>
            </div>

            <div class="product-cut__title">
                <?= Html::a($model->name, ['/shop/product/view', 'slug' => $model->slug], [
                    'class' => 'product-cut__title-link'
                ]) ?>
            </div>
            <div class="product-cut__price">
                <div class="product-price product-price--bg">
                    <?php if($model->getPrice(2)):?>
                    <div class="product-price__item">
                        <div class="product-price__old">
                            <span class="product-price__item-value"><?= $model->getPrice(1) ?></span>
                            <span class="product-price__item-cur">р.</span>
                        </div>
                    </div>
                    <div class="product-price__item">
                        <div class="product-price__main">
                            <span class="product-price__item-value"><?= $model->getPrice(2) ?></span>
                            <span class="product-price__item-cur">р.</span>
                        </div>
                    </div>
                    <?php elseif($model->getPrice(1)):?>
                    <div class="product-price__item">
                        <div class="product-price__main">
                            <span class="product-price__item-value"><?= $model->getPrice(1) ?></span>
                            <span class="product-price__item-cur">р.</span>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </article>
</div>
