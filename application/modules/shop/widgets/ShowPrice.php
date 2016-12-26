<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\widgets;

use app\modules\shop\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class ShowPrice extends \yii\base\Widget
{
    /** @var Product  */
    public $model = NULL;
    public $htmlTag = 'span';
    public $cssClass = '';

    public function init()
    {
        \app\modules\shop\assets\WidgetAsset::register($this->getView());

        return parent::init();
    }

    public function run()
    {
        if($modifications = $this->model->modifications) {
            $json = [];

            foreach($modifications as $modification) {
                $json[$modification->id] = [
                    'product_id' => $modification->product_id,
                    'name' => $modification->name,
                    'code' => $modification->code,
                    'price' => $modification->price,
                    'amount' => $modification->amount,
                    'filter_value' => $modification->filtervariants,
                ];
            }

            $js = 'oakcms.modificationconstruct.modifications = '.json_encode($json).';';

            $this->getView()->registerJs($js);
        }

        if($this->model->getPrice(2)) {
            return
                Html::tag(
                    $this->htmlTag,
                    'Старая цена: <span>'.$this->model->getPrice(1).'</span>',
                    ['class' => "old_prise {$this->cssClass}"]
                ) .
                Html::tag(
                    $this->htmlTag,
                    'Цена: <span>'.$this->model->getPrice(2).'</span>',
                    ['class' => "total_cost oakcms-shop-price oakcms-shop-price-{$this->model->id} {$this->cssClass}"]
                );
        } elseif ($this->model->getPrice(1)) {
            return
                Html::tag(
                    $this->htmlTag,
                    'Цена: <span>'.$this->model->getPrice(1).'</span>',
                    ['class' => "total_cost oakcms-shop-price oakcms-shop-price-{$this->model->id} {$this->cssClass}"]
                );
        }
    }
}
