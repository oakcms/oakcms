<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
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
