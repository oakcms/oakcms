<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\interfaces;

interface ElementService
{
    public function getId();

    public function getItemId();

    public function getCount();

    public function getPrice();

    public function getModel($withCartElementModel);

    public function getOptions();

    public function setItemId($itemId);

    public function setCount($count);

    public function countIncrement($count);

    public function setPrice($price);

    public function setModel($model);

    public function setOptions($options);
}
