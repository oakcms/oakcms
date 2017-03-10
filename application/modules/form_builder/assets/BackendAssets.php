<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\assets;

class BackendAssets extends \yii\web\AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\SimpleLineIconsAsset',
    ];

    public $js = [
        'js/form-builder.min.js',
        'js/form-builder-script.js'
    ];

    public $css = [
        'css/form-builder.min.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web/backend';
        parent::init();
    }
}
