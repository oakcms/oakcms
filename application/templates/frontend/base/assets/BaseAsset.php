<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\templates\frontend\base\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BaseAsset extends AssetBundle
{
    public $sourcePath = '@app/templates/frontend/base/web/';
    public $basePath = '@app/templates/frontend/base/web/';

    public $css = [
        'css/style.css',
    ];

    public $js = [
        'js/script.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\FontAwesome',
        'app\templates\frontend\base\assets\WowAsset',
    ];

    public $jsOptions = [
        'position' => View::POS_END
    ];
}
