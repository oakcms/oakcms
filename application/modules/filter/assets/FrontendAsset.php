<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\assets;

use yii\web\AssetBundle;

class FrontendAsset extends AssetBundle
{
    public $depends = [
        'app\modules\filter\assets\Asset'
    ];

    public $js = [
        'js/frontend.js',
    ];

    public $css = [
        'css/styles.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
