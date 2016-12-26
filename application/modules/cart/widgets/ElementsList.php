<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\widgets;

use app\modules\cart\widgets\DeleteButton;
use app\modules\cart\widgets\TruncateButton;
use app\modules\cart\widgets\ChangeCount;
use app\modules\cart\widgets\CartInformer;
use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class ElementsList extends \yii\base\Widget
{
    const TYPE_DROPDOWN = 'dropdown';
    const TYPE_FULL = 'full';

    public $offerUrl = NULL;
    public $textButton = NULL;
    public $type = NULL;
    public $model = NULL;
    public $cart = NULL;
    public $showTotal = false;
    public $showOptions = true;
    public $showOffer = false;
    public $showTruncate = false;
    public $currency = null;
    public $otherFields = [];
    public $currencyPosition = null;
    public $showCountArrows = true;
    public $columns = 4;

    public function init()
    {
        $paramsArr = [
            'offerUrl' => $this->offerUrl,
            'textButton' => $this->textButton,
            'type' => $this->type,
            'columns' => $this->columns,
            'model' => $this->model,
            'showTotal' => $this->showTotal,
            'showOptions' => $this->showOptions,
            'showOffer' => $this->showOffer,
            'showTruncate' => $this->showTruncate,
            'currency' => $this->currency,
            'otherFields' => $this->otherFields,
            'currencyPosition' => $this->currencyPosition,
            'showCountArrows' => $this->showCountArrows
        ];

        foreach($paramsArr as $key => $value) {
            if($value === 'false') {
                $this->$key = false;
            }
        }

        $this->getView()->registerJs("oakcms.cart.elementsListWidgetParams = ".json_encode($paramsArr));

        if ($this->type == NULL) {
            $this->type = self::TYPE_FULL;
        }

        if ($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute(['/cart/default/index']);
        }

        if ($this->cart == NULL) {
            $this->cart = yii::$app->cart;
        }

        if ($this->textButton == NULL) {
            $this->textButton = yii::t('cart', 'Cart (<span class="oakcms-cart-price">{p}</span>)', ['c' => $this->cart->getCount(), 'p' => $this->cart->getCostFormatted()]);
        }

        if ($this->currency == NULL) {
            $this->currency = yii::$app->cart->currency;
        }

        if ($this->currencyPosition == NULL) {
            $this->currencyPosition = yii::$app->cart->currencyPosition;
        }

        \app\modules\cart\assets\WidgetAsset::register($this->getView());

        return parent::init();
    }

    public function run()
    {
        $elements = $this->cart->elements;

        if (empty($elements)) {
            $cart = Html::tag('div', yii::t('cart', 'Your cart empty'), ['class' => 'oakcms-cart oakcms-empty-cart']);
        } else {
        	$cart = Html::ul($elements, ['item' => function($item, $index) {
                return $this->_row($item);
            }, 'class' => 'oakcms-cart-list']);
		}

        if (!empty($elements)) {
            $bottomPanel = '';

            if ($this->showTotal) {
                $bottomPanel .= Html::tag('div', Yii::t('cart', 'Total') . ': ' . yii::$app->cart->cost . ' '.yii::$app->cart->currency, ['class' => 'oakcms-cart-total-row']);
            }

            if($this->offerUrl && $this->showOffer) {
                $bottomPanel .= Html::a(yii::t('cart', 'Offer'), $this->offerUrl, ['class' => 'oakcms-cart-offer-button btn btn-success']);
            }

            if($this->showTruncate) {
                $bottomPanel .= TruncateButton::widget();
            }

            $cart .= Html::tag('div', $bottomPanel, ['class' => 'oakcms-cart-bottom-panel']);
        }

        $cart = Html::tag('div', $cart, ['class' => 'oakcms-cart']);

        if ($this->type == self::TYPE_DROPDOWN) {
            $button = Html::button($this->textButton.Html::tag('span', '', ["class" => "caret"]), ['class' => 'btn dropdown-toggle', 'id' => 'oakcms-cart-drop', 'type' => "button", 'data-toggle' => "dropdown", 'aria-haspopup' => 'true', 'aria-expanded' => "false"]);
            $list = Html::tag('div', $cart, ['class' => 'dropdown-menu', 'aria-labelledby' => 'oakcms-cart-drop']);
            $cart = Html::tag('div', $button.$list, ['class' => 'oakcms-cart-dropdown dropdown']);
        }

        return Html::tag('div', $cart, ['class' => 'oakcms-cart-block']);
    }

    private function _row($item)
    {
        if (is_string($item)) {
            return html::tag('li', $item);
        }

        $columns = [];

        $product = $item->getModel();

        $allOptions = $product->getCartOptions();

        $cartElName = $product->getCartName();

        if($this->showOptions && $item->getOptions()) {
            $options = '';
            foreach($item->getOptions() as $optionId => $valueId) {
                if($optionData = $allOptions[$optionId]) {
                    $option = $optionData['name'];

                    $value = $optionData['variants'][$valueId];

                    $options .= Html::tag('div', Html::tag('strong', $option) . ':' . $value);
                }
            }

            $cartElName .= Html::tag('div', $options, ['class' => 'oakcms-cart-show-options']);
        }

        if(!empty($this->otherFields)) {
            foreach($this->otherFields as $fieldName => $field) {
                $cartElName .= Html::tag('p', Html::tag('small', $fieldName.': '.$product->$field));
            }
        }

        if($this->columns == 4) {
            $columns[] = Html::tag('div', $cartElName, ['class' => 'col-lg-6 col-md-6 col-xs-6']);
            $columns[] = Html::tag('div', ChangeCount::widget(['model' => $item, 'showArrows' => $this->showCountArrows]), ['class' => 'col-lg-3 col-xs-3']);
            $columns[] = Html::tag('div', $this->_getCostFormatted($item->getCost(false)), ['class' => 'col-lg-2 col-xs-2']);
        } else {
            $columns[] = Html::tag('div', $cartElName, ['class' => 'col-lg-8 col-md-8 col-xs-8']);
            $columns[] = Html::tag('div', $this->_getCostFormatted($item->getCost(false)).ChangeCount::widget(['model' => $item, 'showArrows' => $this->showCountArrows]), ['class' => 'col-lg-3 col-md-3 col-xs-3']);
        }

        $columns[] = Html::tag('div', DeleteButton::widget(['model' => $item, 'lineSelector' => 'oakcms-cart-row ', 'cssClass' => 'delete']), ['class' => 'shop-cart-delete col-lg-1 col-md-1 col-xs-1']);

        $return = html::tag('div', implode('', $columns), ['class' => ' row']);
        return Html::tag('li', $return, ['class' => 'oakcms-cart-row ']);
    }

    private function _getCostFormatted($cost)
    {
        if ($this->currencyPosition == 'after') {
            return "$cost{$this->currency}";
        } else {
            return "{$$this->currency}$cost";
        }
    }
}
