<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\interfaces;

interface CartElement
{
    public function getCartId();

    public function getCartName();

    public function getCartPrice();

    public function getCartOptions();
}
