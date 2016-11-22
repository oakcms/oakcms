<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\widgets;


/**
 * Class PopupAsset
 * @package yii2-widgets
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PopupAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/assets/popup';
    public $js = [
        'js/popup.js',
    ];
    public $css = [
        'css/popup.css'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}
