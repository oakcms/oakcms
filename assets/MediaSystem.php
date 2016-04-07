<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 07.04.2016
 * Project: oakcms
 * File name: OakAdminBar.php
 */

namespace app\assets;

use yii\web\AssetBundle;

class MediaSystem extends AssetBundle
{
    public $sourcePath = '@app/media/';
    public $basePath = '@app/media/';
    public $css = [
        'css/oak-admin-bar.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}