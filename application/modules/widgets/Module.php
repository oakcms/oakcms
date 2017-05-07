<?php

namespace app\modules\widgets;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;

/**
 * widgets module definition class
 */
class Module extends \app\components\module\Module implements ModuleEventsInterface
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

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['widgets'] = [
            'label' => \Yii::t('widgets', 'Widgets'),
            'icon' => '<i class="fa fa-th"></i>',
            'url' => ['/admin/widgets']
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Menu::EVENT_FETCH_ITEMS => 'addAdminMenuItem'
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
