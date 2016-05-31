<?php

namespace app\modules\content;

/**
 * content module definition class
 */
class Module extends \yii\base\Module
{
    public $settings = [
        'title' => 'OakCMS',
    ];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\content\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
