<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 01.06.2016
 * Project: oakcms
 * File name: bootstrapSwitch.php
 */

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class bootstrapSwitch extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-switch/dist';

    public $js = [
        'js/bootstrap-switch.min.js'
    ];

    public $css = [
        'css/bootstrap3/bootstrap-switch.min.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
