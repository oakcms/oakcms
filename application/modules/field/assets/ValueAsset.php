<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\assets;

use yii\web\AssetBundle;

class ValueAsset extends AssetBundle
{
    public $depends = [
        'app\modules\field\assets\Asset'
    ];

    public $js = [
        'js/value.js',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
