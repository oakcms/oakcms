<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: AddClearAsset.php
 */

namespace app\templates\frontend\base\assets;

use yii\web\AssetBundle;

class AddClearAsset extends AssetBundle
{
    public $sourcePath = '@bower/add-clear/';
    public $basePath = '@app/add-clear/';

    public $css = [];
    public $js = [
        'addclear.min.js',
    ];
    public $depends = [];
}
