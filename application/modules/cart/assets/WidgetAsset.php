<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\assets;

use yii\web\AssetBundle;

class WidgetAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $js = [
        'js/scripts.js',
    ];

    public $css = [
        'css/styles.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }
}
