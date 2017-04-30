<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
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
