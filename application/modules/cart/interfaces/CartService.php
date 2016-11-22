<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\interfaces;

interface CartService
{
    public function my();

    public function put(ElementService $model);

    public function getElements();

    public function getElement(CartElement $model, $options);

    public function getCost();

    public function getCount();

    public function getElementById($id);

    public function getElementsByModel(CartElement $model);

    public function truncate();
}
