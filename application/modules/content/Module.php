<?php

namespace app\modules\content;

use app\components\events\FetchRoutersEvent;
use app\components\menu\MenuUrlRule;
use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use app\modules\content\components\MenuRouterContent;
use app\modules\menu\widgets\events\MenuItemRoutesEvent;
use app\modules\menu\widgets\MenuItemRoutes;
use Yii;

/**
 * content module definition class
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    /** @var array The rules to be used in Backend Url management. */
    public static $urlRulesBackend = [];

    /** @var array The rules to be used in Frontend Url management. */
    public static $urlRulesFrontend = [
        'api/page/<slug:[\w\-]+>'                      => 'content/api-page/view',
        'api/content/<catslug:[\w\-]+>/<slug:[\w\-]+>' => 'content/api-article/view',
        'api/content/<slug:[\w\-]+>'                   => 'content/api-category/view',
        'content/<slug:[\w\-]+>'                       => 'content/category/view',
        'content/<catslug:[\w\-]+>/<slug:[\w\-]+>'     => 'content/article/view',
        'page/<slug:[\w\-]+>'                          => 'content/page/view',
    ];

    public $settings = [
        'show_title'           => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'link_titles'          => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_intro'           => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_category'        => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'link_category'        => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_parent_category' => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'link_parent_category' => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_author'          => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'link_author'          => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_create_date'     => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_modify_date'     => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_publish_date'    => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'show_hits'            => [
            'type'  => 'checkbox',
            'value' => false,
        ],
        'categoryThumb'        => [
            'type'  => 'checkbox',
            'value' => false,
        ],
    ];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['content'] = [
            'label' => \Yii::t('content', 'Content'),
            'icon'  => '<i class="fa fa-file-text-o"></i>',
            'items' => [
                [
                    'icon'  => '<i class="fa fa-file-o"></i>',
                    'label' => \Yii::t('content', 'Pages'),
                    'url'   => ['/admin/content/pages/index'],
                ],
                [
                    'icon'  => '<i class="fa fa-file-text-o"></i>',
                    'label' => \Yii::t('content', 'Articles'),
                    'url'   => ['/admin/content/article/index'],
                ],
                [
                    'icon'  => '<i class="fa fa-folder-o"></i>',
                    'label' => \Yii::t('content', 'Categories'),
                    'url'   => ['/admin/content/category/index'],
                ],
            ],
        ];
    }

    /**
     * @param $event MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items['content'] = [
            'label' => Yii::t('content', 'Content'),
            'items' => [
                [
                    'label' => Yii::t('content', 'Category View'),
                    'url'   => [
                        '/admin/content/category/select',
                    ],
                ],
                [
                    'label' => Yii::t('content', 'Article View'),
                    'url'   => [
                        '/admin/content/article/select',
                    ],
                ],
                [
                    'label' => Yii::t('content', 'Page View'),
                    'url'   => [
                        '/admin/content/pages/select',
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
        $event->routers['MenuRouterContent'] = MenuRouterContent::className();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Menu::EVENT_FETCH_ITEMS                 => 'addAdminMenuItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS       => 'addMenuItemRoutes',
            MenuUrlRule::EVENT_FETCH_MODULE_ROUTERS => 'addMenuRouter',
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
