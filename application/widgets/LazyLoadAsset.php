<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\widgets;


use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class LazyLoadAsset
 * @package yii2-widgets
 */
class LazyLoadAsset extends AssetBundle {
    public $sourcePath = '@app/widgets/assets/lazy-load';
    public $js = [
        'dist/jquery.lazyloadxt.extra.min.js',
    ];
    public $css = [
        'dist/jquery.lazyloadxt.fadein.min.css',
        'dist/jquery.lazyloadxt.spinner.min.css',
    ];
    public $jsOptions = [
        'position' => View::POS_END
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
