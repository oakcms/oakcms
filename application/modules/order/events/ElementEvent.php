<?php
namespace app\modules\order\events;

use yii\base\Event;

class ElementEvent extends Event
{
    public $model;
    public $orderModel;
    public $productModel;
}
