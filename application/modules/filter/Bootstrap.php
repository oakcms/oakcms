<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->get('assetManager')->bundles['yii2mod\slider\IonSliderAsset'] =
                [
                    'css' => [
                        'css/normalize.css',
                        'css/ion.rangeSlider.css',
                        'css/ion.rangeSlider.skinNice.css'
                     ]
                ];
    }
}
