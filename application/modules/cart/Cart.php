<?php
namespace app\modules\cart;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use app\modules\cart\events\Cart as CartEvent;
use app\modules\cart\events\CartElement as CartElementEvent;
use app\modules\cart\events\CartGroupModels;
use yii;

class Cart extends Component
{
    const EVENT_CART_INIT = 'cart_init';
    const EVENT_CART_TRUNCATE = 'cart_truncate';
    const EVENT_CART_COST = 'cart_cost';
    const EVENT_CART_COUNT = 'cart_count';
    const EVENT_CART_PUT = 'cart_put';
    const EVENT_CART_ROUNDING = 'cart_rounding';
    const EVENT_MODELS_ROUNDING = 'cart_models_rounding';
    const EVENT_ELEMENT_COST = 'element_cost';
    const EVENT_ELEMENT_PRICE = 'element_price';
    const EVENT_ELEMENT_ROUNDING = 'element_rounding';

    private $cost = 0;
    private $element = null;
    private $cart = null;

    public $currency = NULL;
    public $elementBehaviors = [];
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];

    public function __construct(interfaces\CartService $cartService, interfaces\ElementService $elementService, $config = [])
    {
        $this->cart = $cartService;
        $this->element = $elementService;

        parent::__construct($config);
    }

    public function init()
    {
        $this->trigger(self::EVENT_CART_INIT, new CartEvent(['cart' => $this->cart]));
        $this->update();

        return $this;
    }

    public function put(\app\modules\cart\interfaces\CartElement $model, $count = 1, $options = [])
    {
        if (!$elementModel = $this->cart->getElement($model, $options)) {
            $elementModel = new $this->element;
            $elementModel->setCount((int)$count);
            var_dump($model->getCartPrice());
            var_dump($model->getCartId());
            exit;
            $elementModel->setPrice($model->getCartPrice());
            $elementModel->setItemId($model->getCartId());
            $elementModel->setModel(get_class($model));
            $elementModel->setOptions($options);

            $elementEvent = new CartElementEvent(['element' => $elementModel]);
            $this->trigger(self::EVENT_CART_PUT, $elementEvent);

            if(!$elementEvent->stop) {
                try {
                    $this->cart->put($elementModel);
                } catch (Exception $e) {
                    throw new \yii\base\Exception(current($e->getMessage()));
                }
            }
        } else {
            $elementModel->countIncrement($count);
        }

        return $elementModel;
    }

    public function putWithPrice(\app\modules\cart\interfaces\CartElement $model, $price = 0, $count = 1, $options = [])
    {
        if (!$elementModel = $this->cart->getElement($model, $options)) {
            $elementModel = $this->element;
            $elementModel->setCount((int)$count);
            $elementModel->setPrice($price);
            $elementModel->setItemId($model->getCartId());
            $elementModel->setModel(get_class($model));
            $elementModel->setOptions($options);

            $elementEvent = new CartElementEvent(['element' => $elementModel]);
            $this->trigger(self::EVENT_CART_PUT, $elementEvent);

            if(!$elementEvent->stop) {
                try {
                    $this->cart->put($elementModel);
                } catch (Exception $e) {
                    throw new \yii\base\Exception(current($e->getMessage()));
                }
            }
        } else {
            $elementModel->countIncrement($count);
        }

        return $elementModel;
    }

    public function getElements()
    {
        return $this->cart->elements;
    }

    public function getHash()
    {
        $elements = $this->elements;

        return md5(implode('-', ArrayHelper::map($elements, 'id', 'id')).implode('-', ArrayHelper::map($elements, 'count', 'count')));
    }

    public function getCount()
    {
        $count = $this->cart->getCount();

        $cartEvent = new CartEvent(['cart' => $this->cart, 'count' => $count]);
        $this->trigger(self::EVENT_CART_COUNT, $cartEvent);
        $count = $cartEvent->count;

        return $count;
    }

    public function getCost($withTriggers = true)
    {
        $elements = $this->cart->elements;

        $pricesByModels = [];

        foreach($elements as $element) {
            $price = $element->getCost($withTriggers);

            if (!isset($pricesByModels[$element->model])) {
                $pricesByModels[$element->model] = 0;
            }

            $pricesByModels[$element->model] += $price;
        }

        $cost = 0;

        foreach($pricesByModels as $model => $price) {
            $cartGroupModels = new CartGroupModels(['cart' => $this->cart, 'cost' => $price, 'model' => $model]);
            $this->trigger(self::EVENT_MODELS_ROUNDING, $cartGroupModels);
            $cost += $cartGroupModels->cost;
        }

        $cartEvent = new CartEvent(['cart' => $this->cart, 'cost' => $cost]);

        if($withTriggers) {
            $this->trigger(self::EVENT_CART_COST, $cartEvent);
            $this->trigger(self::EVENT_CART_ROUNDING, $cartEvent);
        }

        $cost = $cartEvent->cost;

        $this->cost = $cost;

        return $this->cost;
    }

    public function getCostFormatted()
    {
        $price = number_format($this->getCost(), $this->priceFormat[0], $this->priceFormat[1], $this->priceFormat[2]);

        if ($this->currencyPosition == 'after') {
            return "<span>$price</span>{$this->currency}";
        } else {
            return "<span>{$this->currency}</span>$price";
        }
    }

    public function getElementsByModel(\app\modules\cart\interfaces\CartElement $model)
    {
        return $this->cart->getElementByModel($model);
    }

    public function getElementById($id)
    {
        return $this->cart->getElementById($id);
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function truncate()
    {
        $this->trigger(self::EVENT_CART_TRUNCATE, new CartEvent(['cart' => $this->cart]));
        $truncate = $this->cart->truncate();
        $this->update();

        return $truncate;
    }

    private function update()
    {
        $this->cart = $this->cart->my();
        $this->cost = $this->cart->getCost();

        return true;
    }
}
