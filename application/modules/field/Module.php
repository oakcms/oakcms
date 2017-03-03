<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\field;

use app\components\module\ModuleEvent;
use app\components\module\ModuleEventsInterface;
use app\modules\admin\rbac\Rbac;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use app\modules\field\events\RelationModelsModuleEvent;

class Module extends \app\components\module\Module implements ModuleEventsInterface
{
    const EVENT_RELATION_MODELS = 'fieldRelationModels';
    public $types = [
        'select' => 'Селект',
        'radio' => 'Радиобатон',
        'checkbox' => 'Чекбокс',
        'date' => 'Дата',
        'numeric' => 'Число',
        'text' => 'Текст',
        'textarea' => 'Текстарея',
        'image' => 'Картинка',
        'textBlock' => 'Text Block'
    ];

    public $_relationModels = [];

    public $adminRoles = [Rbac::PERMISSION_ADMIN_PANEL];

    public function getRelationModels()
    {
        return ModuleEvent::trigger(self::EVENT_RELATION_MODELS, new RelationModelsModuleEvent(['items' => $this->_relationModels]), 'items');
    }

    /**
     * @param array $items
     */
    public function setRelationModels($items)
    {
        $this->_relationModels = $items;
    }

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['field'] = [
            'label' => \Yii::t('field', 'Fields'),
            'icon' => '<i class="fa fa-plus-square"></i>',
            'items' => [
                [
                    'label' => \Yii::t('field', 'Categories'),
                    'url' => ['/admin/field/category/index'],
                    'icon' => '<i class="fa fa-folder-o"></i>'
                ],
                [
                    'label' => \Yii::t('field', 'Items'),
                    'url' => ['/admin/field/field/index'],
                    'icon' => '<i class="fa fa-file-text-o"></i>'
                ],
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
}
