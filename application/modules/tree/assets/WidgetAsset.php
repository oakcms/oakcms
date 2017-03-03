<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\tree\assets;

use yii\web\AssetBundle;

class WidgetAsset extends AssetBundle {

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $js = [
        'js/jquery.nestable.js',
        'js/ui-nestable.js',
        'js/scripts.js',
    ];
    public $css = [
        'css/jquery.nestable.css',
        'css/style.css',
    ];

    public function init() {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
