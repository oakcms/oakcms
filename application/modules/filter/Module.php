<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\rbac\Rbac;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use app\modules\shop\models\Category;

class Module extends \app\components\module\Module implements ModuleEventsInterface
{
    public $relationFieldName = 'category_id';
    public $relationFieldValues = [];
    public $relationFieldValuesCallback = '';

    public $types = [
        'radio' => 'Radio',
        'checkbox' => 'Checkbox',
        'select' => 'Select',
        'range' => 'Промежуток Цены'
    ];
    public $adminRoles = [Rbac::PERMISSION_ADMIN_PANEL];

    public function init()
    {
        $this->relationFieldValues = $this->getCategories();

        if(is_callable($this->relationFieldValues)) {
            $values = $this->relationFieldValues;
            $this->relationFieldValues = $values();
        }

        parent::init();
    }

    function getCategories() {
        //Пример с деревом:
        $return = [];
        $categories = Category::find()->all();
        foreach($categories as $category) {
            if(empty($category->parent_id)) {
                $return[] = $category;
                foreach($categories as $category2) {
                    if($category2->parent_id == $category->id) {
                        $category2->name = ' --- '.$category2->name;
                        $return[] = $category2;
                    }
                }
            }
        }
        return \yii\helpers\ArrayHelper::map($return, 'id', 'name');
    }

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['filter'] = [
            'label' => \Yii::t('filter', 'Filter'),
            'icon' => '<i class="fa fa-filter"></i>',
            'url' => ['/admin/filter/filter/index']
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
