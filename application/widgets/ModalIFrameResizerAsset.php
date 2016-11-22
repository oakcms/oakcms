<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\widgets;


/**
 * Class ModalIFrameResizerAsset
 * @package yii2-widgets
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ModalIFrameResizerAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/assets/modal-iframe';
    public $js = [
        'js/iframeResizer.contentWindow.min.js',
    ];
}
