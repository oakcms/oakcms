<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\bootstrap\Html;
use app\modules\cart\widgets\CartInformer;

/**
 * @var $elements app\modules\cart\Cart[]
 */
$this->title = yii::t('cart', 'Cart');

$this->params['breadcrumb'][] = "Корзина";

?>


<div class="cart">
    <h2><?= Yii::t('cart', 'Cart'); ?></h2>

    <div class="cart-summary ">

        <div class="cart-summary__items">
            <?php foreach($elements as $element): ?>
                <div class="cart__item cart__item-<?= $element->getModel()->id ?>">
                    <div class="cart__item">
                        <div class="image">

                        </div>

                    </div>
                </div>

                <div class="cart-summary__row">

                    <div class="cart-summary__cell cart-summary__cell--delete">
                        <div class="cart-summary__delete">
                            <a class="cart-summary__delete-icon" href="" title="Удалить">x</a>
                        </div>
                    </div>

                    <!-- Product kit -->
                    <div class="cart-summary__cell">
                        <div class="cart-summary__product">
                            <div class="cart-product">

                                <div class="cart-product__photo">

                                    <div class="product-photo">
                                        <a class="product-photo__item product-photo__item--xs" href="http://demoshop.imagecms.net/detskaia-krovatka-sonia-ld">
                                            <?= Html::img($element->getModel()->getImage()->getUrl('200x125')) ?>
                                        </a>
                                    </div>

                                </div>
                                <!-- /.__photo -->

                                <div class="cart-product__info">

                                    <!-- Product brand -->
                                    <div class="cart-product__brand">
                                        Veres
                                    </div>

                                    <!-- Product title -->
                                    <div class="cart-product__title">
                                        <a class="cart-product__link" href="http://demoshop.imagecms.net/detskaia-krovatka-sonia-ld">Детская кроватка Соня ЛД из экологически чистых материалов</a>
                                        <!-- System bonus module -->



                                        <div class="bonus  " data-bonus="">
                                            <span class="bonus__title">Вы получите</span> <span class="bonus__points">+<span data-bonus-points="">18</span> <span data-bonus-label="">баллов</span> </span>
                                        </div>          </div>
                                    <!-- Product option (variant) -->
                                    <p class="cart-product__option">Бук</p>
                                </div><!-- /.__info -->

                            </div><!-- /.__product -->            </div>
                    </div>
                    <!-- END Including products -->


                    <!-- Quantity of product -->
                    <div class="cart-summary__cell">
                        <form class="cart-summary__quantity" action="http://demoshop.imagecms.net/shop/cart/setQuantityProductByVariantId/18014" method="get" data-cart-summary--quantity="" data-cart-summary--href="http://demoshop.imagecms.net/shop/cart/api/setQuantityProductByVariantId/18014">

                            <div class="form-input " data-form-quantity="" data-form-quantity-submit="">
                                <div class="form-input__group">
                                    <div class="form-input__group-item">
                                        <button class="form-input__group-btn" type="button" data-form-quantity-control="minus">-</button>
                                    </div>
                                    <input class="form-input__control form-input__control--quantity" type="text" name="quantity" autocomplete="off" value="1" data-cart-summary--quantity-field="" data-form-quantity-field="" data-form-quantity-step="1">
                                    <div class="form-input__group-item">
                                        <button class="form-input__group-btn" type="button" data-form-quantity-control="plus">+</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>


                    <!-- Product Price -->
                    <div class="cart-summary__cell">
                        <div class="cart-summary__price">

                            <div class="cart-price">
                                <div class="cart-price__main cart-price__main--small">
                                    <span class="cart-price__main-cur">$</span><span class="cart-price__main-value">180</span>              </div>
                            </div>

                        </div>
                    </div>


                </div>
            <?php endforeach; ?>
            <!-- /.__row -->
        </div>

              <!-- Gift coupon -->
        <div class="cart-summary__subtotal">

            <!-- Total origin price -->


            <!-- Total discount -->

            <!-- Total points from system bonus module -->
            <div class="cart-summary__subtotal-item">
                <div class="cart-summary__subtotal-title">
                    Бонусные баллы      </div>
                <div class="cart-summary__subtotal-value">
                    <div class="cart-price">
                        <div class="cart-price__main cart-price__main--small">
                            18            баллов          </div>
                    </div>
                </div>
            </div>


            <!-- Delivery price -->

            <!-- Gift card code -->

        </div><!-- /.__subtotal -->


        <div class="cart-summary__total">

            <!-- Gift coupon. Not visible in order view page -->
            <div class="cart-summary__total-coupon">
                <form class="form" action="http://demoshop.imagecms.net/shop/cart" method="post" data-cart-summary--coupon="">
                    <div class="input-group">
                        <input class="form-control" type="text" name="gift" value="" placeholder="Подарочный сертификат">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit">Активировать</button>
                        </div>
                    </div>


                    <input type="hidden" value="f157347d6e5c73d11cf9200af57563e5" name="cms_token">      </form>
            </div>


            <div class="cart-summary__total-price cart-summary__total-price--order">
                <div class="cart-summary__total-label">
                    К оплате          </div>
                <div class="cart-summary__total-value">
                    <div class="cart-price">
                        <div class="cart-price__main cart-price__main--lg">
                            <span class="cart-price__main-cur">$</span><span class="cart-price__main-value">180</span>        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="cart__items">
        <?php foreach($elements as $element): ?>
            <div class="cart__item cart__item-<?= $element->getModel()->id ?>">
                <div class="cart__item">
                    <div class="image">
                        <?= Html::img($element->getModel()->getImage()->getUrl('200x125')) ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="total">
        <?= CartInformer::widget(['htmlTag' => 'h3']); ?>
    </div>
</div>
