<?php

namespace app\modules\widgets;

/**
 * widgets module definition class
 */
class Module extends \yii\base\Module
{
    public $widgetkit;

    public $settings = [
        'googlemapseapikey' => [
            'type' => 'textInput',
            'value' => '',
        ],
        'disable_frontend_style' => [
            'type' => 'checkbox',
            'value' => '0'
        ]
    ];

    public static function adminMenu() {
        return [
            'label' => \Yii::t('widgets', 'Widgets'),
            'icon' => '<i class="fa fa-th"></i>',
            'url' => ['/admin/widgets'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

    }
}
