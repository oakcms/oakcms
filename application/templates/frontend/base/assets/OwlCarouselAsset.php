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

class OwlCarouselAsset extends AssetBundle
{
    public $sourcePath = '@bower/owl.carousel/dist/';
    public $basePath = '@bower/owl.carousel/dist/';

    public $css = [
        'assets/owl.carousel.min.css',
    ];
    public $js = [
        'owl.carousel.min.js',
    ];
    public $depends = [];
}
