<?php
namespace app\modules\order\assets;

use yii\web\AssetBundle;

class AssigmentToOrder extends AssetBundle
{
    public $depends = [
        'app\modules\order\assets\Asset'
    ];

    public $js = [
        'js/assigment-to-order.js',
    ];

    public $css = [

    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
