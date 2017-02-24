<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.12.2016
 * Project: oakcms
 * File name: MainGalleryAsset.php
 */

namespace app\modules\shop\assets;


use yii\web\AssetBundle;

class MainGalleryAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $js = [
        'js/libs/jquery.zoom.min.js',
        'js/libs/magnific-popup.js',
        'js/libs/mlsMedia.js',
        'js/main-gallery.js',
    ];

    public $css = [
        'css/magnific-popup.css'
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }
}
