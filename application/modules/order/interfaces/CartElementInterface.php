<?php
namespace app\modules\order\models\tools;

interface CartElementInterface
{
    public function getCartName();

    public function getCartPrice();
}
