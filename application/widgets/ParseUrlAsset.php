<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\widgets;


/**
 * Парсер и модификатор урлов на базе https://github.com/medialize/URI.js
 * Class ParseUrlAsset
 * @package yii2-widgets
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ParseUrlAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/assets/modal-iframe';
    public $js = [
        'js/URI.min.js',
    ];
}
