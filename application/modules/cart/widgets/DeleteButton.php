<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\widgets;

use yii\helpers\Html;

class DeleteButton extends \yii\base\Widget
{
    public $text = NULL;
    public $model = NULL;
    public $cssClass = 'btn btn-danger';
    public $lineSelector = 'li';  //Селектор материнского элемента, где выводится элемент

    public function init()
    {
        parent::init();

        \app\modules\cart\assets\WidgetAsset::register($this->getView());

        if ($this->text == NULL) {
            $this->text = '╳';
        }

        return true;
    }

    public function run()
    {
        return Html::a(Html::encode($this->text), ['/cart/element/delete'], ['data-line-selector' => $this->lineSelector, 'class' => 'oakcms-cart-delete-button '.$this->cssClass, 'data-id' => $this->model->getId()]);
    }
}
