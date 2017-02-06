<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: BaseAsset.php
 */

namespace app\assets;

use yii\web\AssetBundle;

class WowAsset extends AssetBundle
{
    public $sourcePath = '@bower/wow/dist/';
    public $basePath = '@bower/wow/dist/';

    public $css = [
    ];
    public $js = [
        'wow.min.js',
    ];
    public $depends = [
    ];
}
