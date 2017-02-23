<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class CartInformer extends \yii\base\Widget
{

    public $text = NULL;
    public $offerUrl = NULL;
    public $cssClass = NULL;
    public $htmlTag = 'span';
	public $showOldPrice = true;

    public function init()
    {
        parent::init();

        \app\modules\cart\assets\WidgetAsset::register($this->getView());

        if ($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute(["/cart/default/index"]);
        }

        if ($this->text === NULL) {
            $this->text = '{c} '. Yii::t('cart', 'on').' {p}';
        }

        return true;
    }

    public function run()
    {
        $cart = yii::$app->cart;

        $text = '';

        if($this->showOldPrice == false | $cart->cost == $cart->getCost(false)) {
            $count = $cart->getCount();
            $cost = $cart->getCostFormatted();

            if($count <= 0 ) $count = '';
            $text = str_replace(['{c}', '{p}'],
                ['<span class="oakcms-cart-count">'.$count.'</span>', '<span class="oakcms-cart-price">'.$cost.'</span>'],
                $this->text
            );
        } else {
            $count = $cart->getCount();
            $cost1 = round($cart->getCost(false));
            $cost2 = round($cart->getCost(false));

            if($count <= 0 ) $count = '';

            $text = str_replace(['{c}', '{p}'],
                ['<span class="oakcms-cart-count">'.$count.'</span>', '<span class="oakcms-cart-price"><s>'.$cost1.'</s>'.$cost2.'</span>'],
                $this->text
            );
        }

        return Html::tag($this->htmlTag, $text, [
                'class' => "oakcms-cart-informer {$this->cssClass}",
        ]);
    }
}
