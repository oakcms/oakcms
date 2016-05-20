<?php

namespace app\modules\seo;

use Yii;

/**
 * seo module definition class
 */
class Module extends \yii\base\Module
{
    public $settings = [];

    public static $installConfig = [
        'title' => [
            'en' => 'Carousel',
            'ru' => 'Карусель',
        ],
        'icon' => 'picture',
        'order_num' => 40,
    ];

    public function adminMenu() {
        return [
            'label' => 'Seo',
            'icon' => '<i class="fa fa-star"></i>',
            'url' => ['/admin/seo']
        ];
    }

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\seo\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
