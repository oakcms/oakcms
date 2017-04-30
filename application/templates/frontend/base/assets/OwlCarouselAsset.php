<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
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
