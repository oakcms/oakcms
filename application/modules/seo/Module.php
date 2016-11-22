<?php

namespace app\modules\seo;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use Yii;

/**
 * seo module definition class
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $settings = [
        'title' => [
            'type' => 'textInput',
            'value' => 'OAKCMS'
        ]
    ];

    public static $installConfig = [];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['seo'] = [
            'label' => Yii::t('seo', 'Seo'),
            'icon' => '<i class="fa fa-star"></i>',
            'url' => ['/admin/seo']
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

        // custom initialization code goes here
    }
}
