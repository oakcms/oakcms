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

class BaseAsset extends AssetBundle
{
    public $sourcePath = '@app/templates/backend/base/web/';
    public $basePath = '@app/templates/backend/base/web/';

    public $css = [
        'https://fonts.googleapis.com/css?family=Roboto:700,400&amp;subset=cyrillic,latin,greek,vietnamese',
        'less/style.less',
    ];
    public $js = [
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\FontAwesome',
        'app\assets\AdminLTE',
    ];
}
