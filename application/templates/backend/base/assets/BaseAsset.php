<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: BaseAsset.php
 */

namespace app\templates\backend\base\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BaseAsset extends AssetBundle
{
    public $sourcePath = '@app/templates/backend/base/web/';
    public $basePath = '@app/templates/backend/base/web/';

    public $css = [
        '//cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',
        'css/uniform.default.min.css',
        'css/switcher.css',
        'less/style.less',
    ];
    public $js = [
        'js/jquery.cookie.js',
        'js/jquery.uniform.min.js',
        'js/jquery.bootstrap-growl.min.js',
        'js/switcher.js',
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\FontAwesome',
        'app\assets\SimpleLineIconsAsset',
        'app\assets\AdminLTE',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
}
