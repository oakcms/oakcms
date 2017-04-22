<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.10.2016
 * Project: osnovasite
 * File name: LightSlider.php
 */

namespace app\templates\frontend\base\assets;

use yii\web\AssetBundle;

class LightSliderAsset extends AssetBundle {
    public $sourcePath = '@bower/lightslider/dist/';
    public $basePath = '@bower/lightslider/dist/';

    public $css = [
        'css/lightslider.min.css'
    ];
    public $js = [
        'js/lightslider.min.js',

    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
