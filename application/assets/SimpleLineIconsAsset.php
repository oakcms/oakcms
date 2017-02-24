<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 19.10.2016
 * Project: oakcms
 * File name: SimpleLineIconsAsset.php
 */

namespace app\assets;


use yii\web\AssetBundle;

class SimpleLineIconsAsset extends AssetBundle
{
    public $sourcePath = '@app/media/';
    public $baseUrl = '@app/media/';

    public $css = [
        'icons/Simple-Line-Icons-Webfont/simple-line-icons.css',
    ];
}
