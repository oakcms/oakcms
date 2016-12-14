<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 13.12.2016
 * Project: oakcms
 * File name: modalAsset.php
 */

namespace app\modules\admin\assets;


use app\components\View;
use yii\web\AssetBundle;

class modalAsset extends AssetBundle
{
    public $sourcePath = '@app/templates/backend/base/web/';
    public $basePath = '@app/templates/backend/base/web/';

    public $css = [
        'https://fonts.googleapis.com/css?family=Roboto:700,400&amp;subset=cyrillic,latin,greek,vietnamese',
        '//cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',
        'css/uniform.default.min.css',
        'css/switcher.css',
        'less/style.less',
    ];
    public $js = [

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\FontAwesome',
        'app\assets\SimpleLineIconsAsset',
        'app\assets\AdminLTE',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
}
