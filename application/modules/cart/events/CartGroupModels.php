<?php
namespace app\modules\cart\events;

use yii\base\Event;

class CartGroupModels extends Event
{
    public $cost;
    public $cart;
    public $model;
}
