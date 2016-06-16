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
            'label' => \Yii::t('language', 'Multilingual'),
            'icon'  => '<i class="fa fa-flag"></i>',
            'items' => [
                ['label' => \Yii::t('language', 'Language'), 'url' => ['/admin/language'], 'icon'  => '<i class="fa fa-flag"></i>'],
                [
                    'label' => \Yii::t('language', 'Text'),
                    'url' => ['/admin/language/source/index'],
                    'icon'  => '<i class="fa fa-font"></i>'
                ],
                [
                    'label' => \Yii::t('language', 'Text'),
                    'url' => ['/admin/language/translate/index'],
                    'icon'  => '<i class="fa fa-flag-checkered"></i>'
                ],
            ],
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
