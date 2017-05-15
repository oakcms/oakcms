<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart;

class Module extends \app\components\module\Module
{
    public $settings = [];

    public static $setAppComponents = [
        'cart' => [
            'class' => 'app\modules\cart\Cart',
            'currency' => 'р.', //Валюта
            'currencyPosition' => 'after', //after или before (позиция значка валюты относительно цены)
            'priceFormat' => [0,'.', ''], //Форма цены
        ],
    ];

    public function init()
    {
        parent::init();
    }
}
