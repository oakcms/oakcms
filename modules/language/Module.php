<?php

namespace app\modules\language;

/**
 * language module definition class
 */
class Module extends \yii\base\Module
{

    public $settings = [];

    public function adminMenu() {
        return [
            'label' => 'Language',
            'icon' => '<i class="fa fa-flag"></i>',
            'url' => ['/admin/language']
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
