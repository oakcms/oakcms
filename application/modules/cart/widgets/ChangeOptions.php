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
use yii;

class ChangeOptions extends \yii\base\Widget
{
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';

    public $model = NULL;
    public $type = NULL;
    public $cssClass = '';
    public $defaultValues = [];

    public function init()
    {
        if ($this->type == NULL) {
            $this->type = self::TYPE_SELECT;
        }

        parent::init();

        \app\modules\cart\assets\WidgetAsset::register($this->getView());

        return true;
    }

    public function run()
    {
        if ($this->model instanceof \app\modules\cart\interfaces\CartElement) {
            $optionsList = $this->model->getCartOptions();
            $changerCssClass = 'oakcms-option-values-before';
            $id = $this->model->getCartId();
        } else {
            $optionsList = $this->model->getModel()->getCartOptions();
            $this->defaultValues = $this->model->getOptions();
            $id = $this->model->getId();
            $changerCssClass = 'oakcms-option-values';
        }

        if (!empty($optionsList)) {
            $i = 1;
            foreach ($optionsList as $optionId => $optionData) {
                if (!is_array($optionData)) {
                    $optionData = [];
                }

                $cssClass = "{$changerCssClass} oakcms-cart-option{$id} ";

                $optionsArray = ['' => $optionData['name']];
                foreach ($optionData['variants'] as $variantId => $value) {
                    $optionsArray[$variantId] = $value;
                }

                if ($this->type == 'select') {

                    $list = Html::dropDownList('cart_options' . $id . '-' . $i,
                        $this->_defaultValue($optionId),
                        $optionsArray,
                        ['data-href' => Url::toRoute(["/cart/element/update"]), 'data-filter-id' => $optionId, 'data-name' => Html::encode($optionData['name']), 'data-id' => $id, 'class' => "form-control $cssClass"]
                    );
                } else {
                    $list = Html::tag('div', Html::tag('strong', $optionData['name']), ['class' => 'oakcms-option-heading']);
                    $list .= Html::radioList('cart_options' . $id . '-' . $i,
                        $this->_defaultValue($optionId),
                        $optionsArray,
                        ['itemOptions' => ['data-href' => Url::toRoute(["/cart/element/update"]), 'data-filter-id' => $optionId, 'data-name' => Html::encode($optionData['name']), 'data-id' => $id, 'class' => $cssClass]]
                    );
                }

                $options[] = Html::tag('div', $list, ['class' => "oakcms-option"]);
                $i++;
            }
        } else {
            return null;
        }

        return Html::tag('div', implode('', $options), ['class' => 'oakcms-change-options ' . $this->cssClass]);
    }

    private function _defaultValue($option)
    {
        if (isset($this->defaultValues[$option])) {
            return $this->defaultValues[$option];
        }

        return false;
    }
}
