<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 08.04.2016
 * Project: oakcms
 * File name: jQuerySimpleSwitch.php
 */

namespace app\assets;


use yii\web\AssetBundle;

class jQuerySimpleSwitch extends AssetBundle
{
    public $sourcePath = '@app/media/vendor/jQuery-Simple-Switch';

    public $css = [
        'simple-switch.min.css'
    ];

    public $js = [
        'jquery.simpleswitch.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}