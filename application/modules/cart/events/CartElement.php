<?php
namespace app\modules\cart\events;

use yii\base\Event;

class CartElement extends Event
{
    public $element;
    public $cost;
    public $stop;
}
