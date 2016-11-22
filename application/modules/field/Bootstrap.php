<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if(empty($app->modules['gridview'])) {
            $app->setModule('gridview', [
                'class' => '\kartik\grid\Module',
            ]);
        }
    }
}
