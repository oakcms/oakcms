<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\assets;

use yii\web\AssetBundle;

class CreateOutcomingAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $js = [
        'js/createoutcoming.js',
    ];

    public $css = [

    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web/frontend';
        parent::init();
    }

}
