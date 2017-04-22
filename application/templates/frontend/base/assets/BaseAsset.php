<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: BaseAsset.php
 */

namespace app\templates\frontend\base\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BaseAsset extends AssetBundle
{
    public $sourcePath = '@app/templates/frontend/base/web/';
    public $basePath = '@app/templates/frontend/base/web/';

    public $css = [
        'css/animations.css',
        'external/owl.carousel/dist/assets/owl.carousel.min.css',
        'css/sweetalert.css',
        'css/concated.css',
        'css/main.css',
        '//fonts.googleapis.com/css?family=Roboto:400,500,700&subset=cyrillic-ext'
    ];

    public $js = [
        'external/jquery.browser/dist/jquery.browser.min.js',
        'external/wow/dist/wow.js',
        'external/owl.carousel/dist/owl.carousel.min.js',
        'external/textillate/jquery.textillate.js',
        'external/letteringjs/jquery.lettering.js',
        'js/sweetalert.min.js',
        'js/validate.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\FontAwesome',
        'app\templates\frontend\base\assets\WowAsset',
    ];

    public $jsOptions = [
        'position' => View::POS_END
    ];
}
