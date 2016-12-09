<?php
namespace app\modules\order\interfaces;

interface Stock
{
    function getAmount($productId);

	function outcoming($stockId, $productId, $count, $orderId = null);
}
