<?php
namespace app\modules\relations\assets;

use yii\web\AssetBundle;

class WindowAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\FontAwesome'
    ];

    public $js = [
        'js/scripts.js',
    ];

    public $css = [
        'css/styles.css',
    ];

    public function init()
    {
        $this->sourcePath = dirname(__DIR__).'/web';
        $this->publishOptions['forceCopy'] = true;
        parent::init();
    }
}
