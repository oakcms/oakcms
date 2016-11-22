<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\behaviors;

use yii\base\Behavior;
use app\modules\cart\Cart;

class Discount extends Behavior
{
    public $persent = 0;

    public function events()
    {
        return [
            Cart::EVENT_CART_COST => 'doDiscount'
        ];
    }

    public function doDiscount($event)
    {
        if($this->persent > 0 && $this->persent <= 100 && $event->cost > 0) {
            $event->cost = $event->cost-($event->cost*$this->persent)/100;
        }

        return $this;
    }
}
