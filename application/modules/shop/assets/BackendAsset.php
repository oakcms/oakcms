<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\assets;

use yii\web\AssetBundle;

class BackendAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $js = [
        'js/scripts.js',
        'js/stickyeah.js',
        'js/modifications_product.js',
    ];

    public $css = [
        'css/styles.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web/backend';
        parent::init();
    }
}
