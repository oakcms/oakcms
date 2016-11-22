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
        'scss/main.scss',
    ];

    public $js = [

    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
}
