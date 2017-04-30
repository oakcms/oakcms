<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\templates\frontend\base\assets;

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
