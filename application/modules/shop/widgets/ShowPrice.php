<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\widgets;

use app\modules\shop\models\Modification;
use app\modules\shop\models\Product;
use yii\helpers\Html;
use yii\helpers\Json;

class ShowPrice extends \yii\base\Widget
{
    /** @var Product */
    public $model = null;

    /** @var integer */
    public $modificationId = null;

    /** @var null|string */
    public $htmlChangeID = null;

    /** @var string */
    public $htmlTag = 'span';

    /** @var string */
    public $cssClass = '';

    /** @var string */
    public $template = '{priceTemplate}{priceActionTemplate}';

    /** @var string */
    public $priceTemplate = '<div class="total_cost">{price}{currency}</div>';

    /** @var string */
    public $priceActionTemplate = '<div class="old_prise">{price}{currency}</div><div class="total_cost">{price_action}{currency}</div>';

    /** @var string */
    public $currency = '';

    /** @var integer */
    public $replacePriceID = null;

    public $priceType = null;

    public $parts = [];

    public $options = [];

    protected static $_count = 0;

    public function init()
    {
        \app\modules\shop\assets\WidgetAsset::register($this->getView());

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        static::$_count++;

        return parent::init();
    }

    public function run()
    {
        $return = '';
        $notAvailable = Html::tag('div', \Yii::t('shop', 'Нет в наявности'), ['class' => 'not-aviable']);
        $modifications = $this->model->modifications;
        $template = $this->template;
        if (count($modifications) > 0) {
            $json = [];

            $i = 0;
            foreach ($modifications as $modification) {
                $modPrice = $modification->getPrice($this->priceType, true);

                $json[$modification->id] = [
                    'product_id'   => $modification->product_id,
                    'name'         => $modification->name,
                    'code'         => $modification->code,
                    'price'        => $modPrice->price,
                    'price_action' => $modPrice->price_action,
                    'amount'       => $modification->amount,
                    'available'    => $modPrice->available,
                    'filter_value' => $modification->filtervariants,
                    'index'        => $i,
                ];
                $i++;
            }

            if(isset($this->modificationId)) {
                $modification = Modification::findOne($this->modificationId);
            } else {
                $modification = $this->model->modifications[0];
            }
            
            $modPrice = $modification->getPrice($this->priceType, true);
            if ($modPrice !== null && $modPrice->available == 'yes') {
                if ($modPrice->price_action > 0) {
                    $this->parts['{price}'] = $modPrice->price;
                    $this->parts['{price_action}'] = '<span class="oakcms-shop-price oakcms-shop-price-' . $this->model->id . '">' .
                        $modPrice->price_action .
                        '</span>';

                    $template = strtr(
                        $template,
                        [
                            '{priceTemplate}'       => '',
                            '{priceActionTemplate}' => $this->priceActionTemplate,
                        ]
                    );

                } else if ($modPrice->price > 0) {
                    $this->parts['{price_action}'] = '';
                    $this->parts['{price}'] = '<span class="oakcms-shop-price oakcms-shop-price-' . $this->model->id . '">' .
                        $modPrice->price .
                        '</span>';

                    $template = strtr(
                        $template,
                        [
                            '{priceTemplate}'       => $this->priceTemplate,
                            '{priceActionTemplate}' => '',
                        ]
                    );
                } else {
                    $template = $notAvailable;
                }

                $this->parts['{currency}'] = $this->currency;

                $return = strtr(
                    $template,
                    $this->parts
                );
            } else {

                $return = $notAvailable;
            }
        } else {
            $json = [];
        }

        $js = '';
        if(static::$_count == 1) {
            $js = 'oakcms.modificationconstruct.modifications = ' . json_encode($json) . ';';
        }

        if($this->htmlChangeID) {
            $optionsChange = [
                $this->htmlChangeID => [
                    'id'                  => $this->options['id'],
                    'currency'            => $this->currency,
                    'template'            => $this->template,
                    'priceTemplate'       => $this->priceTemplate,
                    'priceActionTemplate' => $this->priceActionTemplate,
                    'notAvailable'        => $notAvailable
                ]
            ];
            $js .= 'oakcms.modificationconstruct.optionsChange = ' . Json::encode($optionsChange) . ';';
        }

        $this->getView()->registerJs($js);

        return Html::tag('div', $return, $this->options);
    }
}
