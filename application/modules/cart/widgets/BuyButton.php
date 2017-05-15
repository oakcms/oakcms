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

class BuyButton extends \yii\base\Widget
{
    public $text = NULL;
    public $model = NULL;
    public $count = 1;
    public $price = false;
    public $description = '';
    public $cssClass = NULL;
    public $htmlTag = 'a';
    public $options = null;

    public function init()
    {
        parent::init();

        \app\modules\cart\assets\WidgetAsset::register($this->getView());

        if ($this->options === NULL) {
            $this->options = (object)[];
        }

        if ($this->text === NULL) {
            $this->text = Yii::t('cart', 'Buy');
        }

        if ($this->cssClass === NULL) {
            $this->cssClass = 'btn btn-success';
        }

        return true;
    }

    public function run()
    {
        if (!is_object($this->model) | !$this->model instanceof \app\modules\cart\interfaces\CartElement) {
            return false;
        }

        $model = $this->model;
        return Html::tag($this->htmlTag, $this->text, [
            'href' => Url::to(['/cart/element/create']),
            'class' => "oakcms-cart-buy-button oakcms-cart-buy-button{$this->model->getCartId()} {$this->cssClass}",
            'data-id' => $model->getCartId(),
            'data-count' => $this->count,
            'data-price' => (int)$this->price,
            'data-options' => json_encode($this->options),
            'data-description' => $this->description,
            'data-model' => $model::className()
        ]);
    }
}
