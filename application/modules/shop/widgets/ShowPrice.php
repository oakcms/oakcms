<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\widgets;

use app\components\ShortCode;
use app\modules\shop\models\Product;
use yii\helpers\Html;

class ShowPrice extends \yii\base\Widget
{
    /** @var Product  */
    public $model = NULL;

    /** @var integer */
    public $modificationId = NULL;

    /** @var string */
    public $htmlTag = 'span';

    /** @var string */
    public $cssClass = '';

    /** @var string */
    public $template = '<div class="old_prise">[price id="1"]{currency}</div><div class="total_cost">[price id="2"]{currency}</div>';

    /** @var string */
    public $currency = '';

    /** @var integer */
    public $replacePriceID = NULL;

    public function init()
    {
        \app\modules\shop\assets\WidgetAsset::register($this->getView());

        return parent::init();
    }

    public function run()
    {
        if($modifications = $this->model->modifications) {
            $json = [];

            $i = 0;
            foreach($modifications as $modification) {
                $json[$modification->id] = [
                    'product_id' => $modification->product_id,
                    'name' => $modification->name,
                    'code' => $modification->code,
                    'price' => $modification->price,
                    'amount' => $modification->amount,
                    'filter_value' => $modification->filtervariants,
                    'index' => $i,
                ];
                $i++;
            }

            $js = 'oakcms.modificationconstruct.modifications = '.json_encode($json).';';

            $this->getView()->registerJs($js);
        } else {
            $json = [];
        }

        $this->template = (new ShortCode())->parse('price', $this->template, function ($attrs) use ($json) {
            if(isset($attrs['id'])) {
                if($this->modificationId !== null) {
                    $return = '<span class="oakcms-shop-price oakcms-shop-price-'.$this->model->id.'">' .
                        $json[$this->modificationId]['price'] .
                    '</span>';
                } elseif($this->replacePriceID !== null && $this->replacePriceID == $attrs['id']) {
                    $return = '<span class="oakcms-shop-price oakcms-shop-price-'.$this->model->id.'">' .
                        $this->model->getPrice($attrs['id']) .
                    '</span>';
                } else {
                    $return = $this->model->getPrice($attrs['id']);
                }
                return $return;
            }
            return '';
        });

        return str_replace('{currency}', $this->currency, $this->template);
    }
}
