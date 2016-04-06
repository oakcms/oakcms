<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 05.04.2016
 * Project: oakcms
 * File name: AdminLTE.php
 */


namespace app\assets;

use yii\web\AssetBundle;


class AdminLTE extends AssetBundle
{
    public $sourcePath = '@bower/admin-lte/dist';

    public $js = [
        'js/app.min.js'
    ];

    public $css = [
        'css/AdminLTE.min.css',
        'css/skins/_all-skins.min.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\FontAwesome',
        'app\assets\JquerySlimScroll'
    ];
}