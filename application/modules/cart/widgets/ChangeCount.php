<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\widgets;

use yii\helpers\Url;
use yii\helpers\Html;

class ChangeCount extends \yii\base\Widget
{
    public $model = NULL;
    public $lineSelector = 'li'; //Селектор материнского элемента, где выводится элемент
    public $downArr = '⟨';
    public $upArr = '⟩';
    public $cssClass = 'oakcms-change-count';
    public $defaultValue = 1;
    public $showArrows = true;

    public function init()
    {
        parent::init();

        \app\modules\cart\assets\WidgetAsset::register($this->getView());

        return true;
    }

    public function run()
    {
        if($this->showArrows) {
            $downArr = Html::tag('span', Html::a($this->downArr, '#', ['class' => 'oakcms-arr oakcms-downArr btn btn-default']), ['class' => 'input-group-btn']);
            $upArr = Html::tag('span', Html::a($this->upArr, '#', ['class' => 'oakcms-arr oakcms-upArr btn btn-default']), ['class' => 'input-group-btn']);
        } else {
            $downArr = $upArr = '';
        }

        if(!$this->model instanceof \app\modules\cart\interfaces\CartElement) {
            $input = Html::activeTextInput($this->model, 'count', [
                'type' => 'text',
                'class' => 'oakcms-cart-element-count',
                'data-line-selector' => $this->lineSelector,
                'data-id' => $this->model->getId(),
                'data-href' => Url::toRoute("/cart/element/update"),
            ]);
        } else {
            $input = Html::input('text', 'count', $this->defaultValue, [
                'class' => 'oakcms-cart-element-before-count form-control',
                'data-line-selector' => $this->lineSelector,
                'data-id' => $this->model->getCartId(),
            ]);
        }

        return Html::tag('div', Html::tag('div', $downArr.$input.$upArr, ['class' => 'input-group']), ['class' => 'oakcms-change-count '.$this->cssClass]);
    }
}
