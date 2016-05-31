<?php

namespace app\modules\seo;

use Yii;

/**
 * seo module definition class
 */
class Module extends \yii\base\Module
{
    public $settings = [
        'title' => 'OakCMS'
    ];

    public static $installConfig = [];

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
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
