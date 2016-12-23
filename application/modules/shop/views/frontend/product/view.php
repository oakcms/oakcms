<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.12.2016
 * Project: oakcms
 * File name: view.php
 *
 * @var $this \app\components\CoreView;
 * @var $model \app\modules\shop\models\Product;
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->bodyClass[] = 'product_page';

$this->params['breadcrumbs'][] = [
    'url' => ['/shop/category/view', 'slug' => $model->category->slug],
    'label' => $model->category->name
];
$this->params['breadcrumbs'][] = $model->name;
?>

<div class="title text-center">
    <span><?= $model->name ?></span>
</div>
<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="block_header ">

            <div class="manufacturer text-left">
                <img src="img/manufactured.png" alt="">
                Производитель: <?= Html::a($model->producer->name, ['/shop/producer/view', 'slug' => $model->producer->slug]) ?>
            </div>

            <div class="question text-right">
                <img src="img/question.png" alt="" style="height: 20px;">
                <a href="#">Задать вопрос по этому товару</a>
            </div>
        </div>


    </div>

    <div class="col-md-6 col-sm-12  ">
        <div class="block_header row ">
            <div class=" article col-md-2 col-sm-12 no_padding">
                Aртикул: <span><?= $model->code ?></span>
            </div>
            <div class=" rating text-center col-sm-12 col-md-7">
                Рейтинг:
                <ul class="list-inline list-unstyled">
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                    <li><img src="img/stars.png" alt=""></li>
                </ul>
                (<span>10</span> голосов)

            </div>

            <div class=" text-right col-md-3 col-sm-12 garanty no_padding ">
                Гарантия 12 месяцев
            </div>
        </div>

    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 no_padding_left">
            <div class="item_content">
                <div class="img_big">
                    <img src="img/big_img.png" alt="">
                </div>

                <div class="col-md-12 mobile_padding no_padding_left">
                    <div class="soc_lis_item ">
                    <span>
                        Поделится c друзями:
                    </span>
                        <ul class="list-inline list-unstyled">
                            <li><a href="href=#"><img src="img/f_vk.png" alt="vk"></a></li>
                            <li><a href="href=#"><img src="img/f_ok.png" alt="ok"></a></li>
                            <li><a href="href=#"><img src="img/f_fb.png" alt="fb"></a></li>
                            <li><a href="href=#"><img src="img/f_tw.png" alt="fb"></a></li>
                            <li><a href="href=#"><img src="img/f_gl.png" alt="google"></a></li>
                            <li><a href="href=#"><img src="img/f_sm.png" alt="sm"></a></li>
                        </ul>
                    </div>

                </div>
                <div class="img_desk hidden-xs hidden-sm ">
                    <div class="col-md-3 no_padding_left ">
                        <img src="img/big_img.png" alt="">
                    </div>
                    <div class="col-md-3  no_padding_left">
                        <img src="img/big_img.png" alt="">
                    </div>
                    <div class="col-md-3 no_padding_left">
                        <img src="img/big_img.png" alt="">
                    </div>
                    <div class="col-md-3 no_padding ">
                        <img src="img/big_img.png" alt="">
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-3">
            <ul class="product-params subject hidden-xs hidden-sm">
                <li>
                    <span class="product-params__title">Габариты(ШхВхГ):</span>
                    <span class="product-params__value">
                        <?= $model->getField('dimensions_width'); ?>х
                        <?= $model->getField('dimensions_height'); ?>х
                        <?= $model->getField('dimensions_depth'); ?>(мм)
                    </span>
                </li>
                <li>
                    <span class="product-params__title">Матириал:</span>
                    <span class="product-params__value"><?= $model->getField('material'); ?></span>
                </li>
                <li>
                    <span class="product-params__title">Гарантия:</span>
                    <span class="product-params__value"><?= $model->getField('guarantee'); ?></span>
                </li>
            </ul>

            <span class="text-right variants hidden-xs hidden-sm ">Варианты цветовго исполнения:</span>

            <div class="colors row hidden-xs hidden-sm">
                <div class="col-md-4  ">
                    <img src="img/colors.png" alt="">
                </div>
                <div class="col-md-4  ">
                    <img src="img/colors.png" alt="">
                </div>
                <div class="col-md-4  ">
                    <img src="img/colors.png" alt="">
                </div>


            </div>
            <?= \app\modules\shop\widgets\ShowPrice::widget(['model' => $model, 'htmlTag' => 'p', 'cssClass' => 'no_padding col-xs-12 mob_center']) ?>

            <div class="quantity total  col-xs-12 mob_center">
                <button class="minus" style="font-size: 37px;">
                    -
                </button>

                <input id="count" type="text" value="1">

                <button class="plus">
                    +
                </button>

            </div>


            <div class="buy_block mob_center">
                <button class="buy_button">
                    купить
                </button>



                <img class="margin_left hidden-xs hidden-sm" src="img/icon-like.png" alt="">
                <img class="hidden-xs hidden-sm" src="img/icon-home.png" alt="">

                <a class="mob_center" style="display:block;" href="#">Купить в 1 клик</a>

            </div>



        </div>
        <div class="col-md-3 no_padding_right hidden-xs hidden-sm">
            <div class="right_side_bar">
                <img src="img/details.png" alt="">
                <span class="name_sidebar">
                    Подробние условия:
                </span>
                <span class="name_list">Доставка</span>
                <ul class="list-unstyled">
                    <li><span class="bold_text">Доставка по Москве</span> - безплатно</li>
                    <li><span class="bold_text">Доставка по МО за МКАД</span> - 100p/км</li>
                    <li><span class="bold_text">Доставка по России</span> - до</li>
                    <li>транспортной компании - безплатно</li>
                </ul>
                <span class="name_list">Ожидаемое время доставки</span>
                <ul class="list-unstyled">
                    <li><span class="bold_text">Москва и область</span>- 1-2 дня</li>
                    <li><span class="bold_text">По России</span>- 1-2 дней</li>
                </ul>
                <span class="name_list">Оплата </span>
                <ul class="list-unstyled">
                    <li><span class="bold_text">Москва и МО</span> наличными при</li>
                    <li>получении, безналичными</li>
                    <li><span class="bold_text">Россия</span>- безналичный расчет</li>
                </ul>
                <span class="name_list">Гарантия 12 месяцев</span>
                <ul class="list-unstyled">
                    <li><span class="bold_text">12 месяцев </span> официальной гарантии</li>
                    <li>от производтеля</li>
                    <li>Обмен/возврат товара в течении</li>
                    <li>14 дней, при невскритой упаковке</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="row">

</div>

<?= $model->text ?>
