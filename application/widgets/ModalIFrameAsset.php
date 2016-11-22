<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\widgets;


/**
 * Class ModalIFrameAsset
 */
class ModalIFrameAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/assets/modal-iframe';
    public $js = [
        'js/iframeResizer.min.js',
        'js/iframe.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'app\widgets\PopupAsset'
    ];
}
