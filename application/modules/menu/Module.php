<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\menu;


use app\components\module\ModuleEvent;
use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use app\modules\menu\events\MenuItemLayoutsModuleEvent;
use Yii;

/**
 * Class Module
 * @package oakcms
 * @author Volodumur Hryvinskiy <script@email.ua>
 */

class Module extends \yii\base\Module implements ModuleEventsInterface
{
    const EVENT_MENU_ITEM_LAYOUTS = 'menuItemLayouts';

    public $controllerNamespace = 'app\modules\menu\controllers';
    public $defaultRoute = 'backend/item';

    public $settings = [];

    private $_menuItemLayouts = [];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['menu'] = [
            'label' => Yii::t('menu', 'Menu'),
            'icon' => '<i class="fa fa-bars"></i>',
            'items' => [
                [
                    'label' => \Yii::t('menu', 'Menus'),
                    'icon' => '<i class="fa fa-bars"></i>',
                    'url' => ['/admin/menu/type']

                ],
                [
                    'label' => \Yii::t('menu', 'Menus Items'),
                    'icon' => '<i class="icon-list" style="width: 20px;display: inline-block;"></i>',
                    'url' => ['/admin/menu/item']
                ]
            ]
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
     * @return array
     */
    public function getMenuItemLayouts()
    {
        return ModuleEvent::trigger(self::EVENT_MENU_ITEM_LAYOUTS, new MenuItemLayoutsModuleEvent(['items' => $this->_menuItemLayouts]), 'items');
    }

    /**
     * @param array $items
     */
    public function setMenuItemLayouts($items)
    {
        $this->_menuItemLayouts = $items;
    }
}
