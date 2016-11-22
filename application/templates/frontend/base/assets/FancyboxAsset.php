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

class FancyboxAsset extends AssetBundle
{
    public $sourcePath = '@bower/fancybox/source/';
    public $basePath = '@bower/fancybox/source/';

    public $css = [
        'jquery.fancybox.css'
    ];
    public $js = [
        'jquery.fancybox.pack.js',

    ];
    public $depends = [
    ];
}
