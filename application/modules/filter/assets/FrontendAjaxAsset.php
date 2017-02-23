<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\assets;

use yii\web\AssetBundle;

class FrontendAjaxAsset extends AssetBundle
{
    public $depends = [
        'app\modules\filter\assets\FrontendAsset'
    ];

    public $js = [
        'js/frontend_ajax.js',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
