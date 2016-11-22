<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop;

use yii\base\BootstrapInterface;
use yii;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if(!$app->has('stock')) {
            $app->set('stock', ['class' => 'app\modules\shop\Stock']);
        }
    }
}
