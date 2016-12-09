<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\rbac\Rbac;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use yii;

class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $adminRoles = [Rbac::PERMISSION_ADMIN_PANEL];
    public $modelMap = [];
    public $defaultTypeId = null;
    public $priceType = null; //callable, возвращающая type_id цены
    public $categoryUrlPrefix = '/admin/shop/category/view';
    public $productUrlPrefix = '/admin/shop/product/view';
    public $oneC = null;
    public $userModel = null;
    public $users = [];
    public $settings = [];

    public $menu = [
        [
            'label' => 'Товары',
            'url' => ['/admin/shop/product/index'],
            'icon' => '<i class="fa fa-camera"></i>'
        ],
        [
            'label' => 'Категории',
            'url' => ['/admin/shop/category/index'],
            'icon' => '<i class="fa fa-folder"></i>'
        ],
        [
            'label' => 'Производители',
            'url' => ['/admin/shop/producer/index'],
            'icon' => '<i class="fa fa-industry"></i>'
        ],
        [
            'label' => 'Склады',
            'url' => ['/admin/shop/stock/index'],
            'icon' => '<i class="fa fa-archive"></i>'
        ],
        [
            'label' => 'Типы цен',
            'url' => ['/admin/shop/price-type/index'],
            'icon' => '<i class="fa fa-product-hunt"></i>'
        ]
    ];

    const EVENT_PRODUCT_CREATE = 'create_product';
    const EVENT_PRODUCT_DELETE = 'delete_product';
    const EVENT_PRODUCT_UPDATE = 'update_product';


    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['shop'] = [
            'label' => \Yii::t('shop', 'Shop'),
            'icon' => '<i class="fa fa-shopping-bag"></i>',
            'items' => $this->menu
        ];
    }

    public function addFieldRelationModel($event) {
        $event->items['app\modules\shop\models\Product'] = 'Продукты';
        $event->items['app\modules\shop\models\Category'] = 'Категории';
        $event->items['app\modules\shop\models\Producer'] = 'Производители';
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Menu::EVENT_FETCH_ITEMS => 'addAdminMenuItem',
            \app\modules\field\Module::EVENT_RELATION_MODELS => 'addFieldRelationModel'
        ];
    }

    public function init()
    {
        if(empty($this->modelMap)) {
            $this->modelMap = [
                'product' => '\app\modules\shop\models\Product',
                'category' => '\app\modules\shop\models\Category',
                'incoming' => '\app\modules\shop\models\Incoming',
                'outcoming' => '\app\modules\shop\models\Outcoming',
                'producer' => '\app\modules\shop\models\Producer',
                'price' => '\app\modules\shop\models\Price',
                'stock' => '\app\modules\shop\models\Stock',
                'modification' => '\app\modules\shop\models\Modification',
            ];
        }

        if(!$this->userModel) {
            if($user = Yii::$app->user->getIdentity()) {
                $this->userModel = $user::className();
            }
        }

        if(is_callable($this->users)) {
            $func = $this->users;
            $this->users = $func();
        }

        parent::init();
    }

    //возвращает type_id цены, которую стоит отобразить покупателю
    public function getPriceTypeId($product = null)
    {
        if(is_callable($this->priceType))
        {
            $priceType = $this->priceType;
            return $values($product);
        }

        return $this->defaultTypeId;
    }

    public function getService($key)
    {
        $model = $this->modelMap[$key];

        return new $model;
    }
}
