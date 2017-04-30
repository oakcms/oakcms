<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\shop;

use app\components\events\FetchRoutersEvent;
use app\modules\menu\behaviors\MenuUrlRule;
use app\components\module\ModuleEventsInterface;
use app\modules\admin\rbac\Rbac;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use app\modules\menu\widgets\events\MenuItemRoutesEvent;
use app\modules\menu\widgets\MenuItemRoutes;
use app\modules\shop\components\MenuRouterShop;
use app\modules\field\Module as FieldModule;
use Yii;

class Module extends \yii\base\Module implements ModuleEventsInterface
{
    const EVENT_PRODUCT_CREATE = 'create_product';
    const EVENT_PRODUCT_DELETE = 'delete_product';
    const EVENT_PRODUCT_UPDATE = 'update_product';

    /** @var array The rules to be used in Frontend Url management. */
    public static $urlRulesFrontend = [
        '/shop/category/<slug:[\w\-]+>' => '/shop/category/view',
        '/shop/product/<slug:[\w\-]+>'  => '/shop/product/view',
    ];

    public $adminRoles = [Rbac::PERMISSION_ADMIN_PANEL];
    public $modelMap = [];
    public $defaultTypeId = null;
    public $priceType = null;
    public $categoryUrlPrefix = '/shop/category/view';
    public $productUrlPrefix = '/shop/product/view';
    public $oneC = null;
    public $userModel = null;
    public $users = [];
    public $settings = [];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['shop'] = [
            'label' => \Yii::t('shop', 'Shop'),
            'icon'  => '<i class="fa fa-opencart"></i>',
            'items' => [
                [
                    'label' => Yii::t('shop', 'Products'), // Товары
                    'url'   => ['/admin/shop/product/index'],
                    'icon'  => '<i class="fa fa-camera"></i>',
                ],
                [
                    'label' => Yii::t('shop', 'Categories'), // Категории
                    'url'   => ['/admin/shop/category/index'],
                    'icon'  => '<i class="fa fa-folder"></i>',
                ],
                [
                    'label' => Yii::t('shop', 'Manufacturers'), // Производители,
                    'url'   => ['/admin/shop/producer/index'],
                    'icon'  => '<i class="fa fa-industry"></i>',
                ],
                [
                    'label' => Yii::t('shop', 'Warehouses'), // Склады
                    'url'   => ['/admin/shop/stock/index'],
                    'icon'  => '<i class="fa fa-archive"></i>',
                ],
                [
                    'label' => Yii::t('shop', 'Types of prices'), // Типы цен
                    'url'   => ['/admin/shop/price-type/index'],
                    'icon'  => '<i class="fa fa-product-hunt"></i>',
                ],
            ],
        ];
    }

    /**
     * @param $event MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items['shop'] = [
            'label' => Yii::t('shop', 'Shop'),
            'items' => [
                [
                    'label' => Yii::t('shop', 'Category'),
                    'url'   => [
                        '/admin/shop/category/select',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $event FetchRoutersEvent
     */
    public function addMenuRouter($event)
    {
        $event->routers['MenuRouterShop'] = MenuRouterShop::className();
    }


    public function addFieldRelationModel($event)
    {
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
            Menu::EVENT_FETCH_ITEMS                 => 'addAdminMenuItem',
            FieldModule::EVENT_RELATION_MODELS      => 'addFieldRelationModel',
            MenuItemRoutes::EVENT_FETCH_ITEMS       => 'addMenuItemRoutes',
            MenuUrlRule::EVENT_FETCH_MODULE_ROUTERS => 'addMenuRouter',
        ];
    }

    public function init()
    {
        if (Yii::$app instanceof \yii\web\Application) {
            if (!$this->userModel) {
                if ($user = Yii::$app->user->getIdentity()) {
                    $this->userModel = $user::className();
                }
            }

            if (is_callable($this->users)) {
                $func = $this->users;
                $this->users = $func();
            }
        }
        parent::init();
    }
}
