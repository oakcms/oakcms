<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 *
 * @var $this \app\components\View
 * @var $elements \app\modules\cart\models\CartElement
 */

use app\modules\cart\widgets\CartInformer;

$this->title = yii::t('cart', 'Cart');

$this->params['breadcrumb'][] = "Корзина";

?>


<div class="cart">
    <h2><?= yii::t('cart', 'Cart'); ?></h2>
    <div class="cart__items">

    </div>
    <?php foreach($elements as $element): ?>
        <div class="cart__items cart__items-<?= $element->getModel()->id ?>">
            <div class="cart__item">
                <div class="image">
                    <?= \yii\bootstrap\Html::img($element->getModel()->getImage()->getUrl('200x125')) ?>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
    <div class="total">
        <?= CartInformer::widget(['htmlTag' => 'h3']); ?>
    </div>
</div>
