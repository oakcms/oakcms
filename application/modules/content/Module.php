<?php

namespace app\modules\content;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use Yii;

/**
 * content module definition class
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $settings = [
        'show_title'            => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'link_titles'           => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_intro'            => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_category'         => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'link_category'         => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_parent_category'  => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'link_parent_category'  => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_author'           => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'link_author'           => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_create_date'      => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_modify_date'      => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_publish_date'     => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'show_hits'             => [
            'type' => 'checkbox',
            'value' => false,
        ],
        'categoryThumb'         => [
            'type' => 'checkbox',
            'value' => false,
        ]
    ];

    /** @var array The rules to be used in Backend Url management. */
    public static $urlRulesBackend = [
        //'content/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:[\w\-]+>' => 'content/<_c>/<_a>',
    ];

    /** @var array The rules to be used in Frontend Url management. */
    public static $urlRulesFrontend = [
        'content/<slug:[\w\-]+>'                        => 'content/category/view',
        'content/<slug:[\w\-]+>'                        => 'content/category/view',
        'content/<catslug:[\w\-]+>/<slug:[\w\-]+>'      => 'content/article/view',
        'page/<slug:[\w\-]+>'                           => 'content/page/view',
    ];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['content'] = [
            'label' => \Yii::t('content', 'Content'),
            'icon' => '<i class="fa fa-file-text-o"></i>',
            'items' => [
                [
                    'icon' => '<i class="fa fa-file-o"></i>',
                    'label' => \Yii::t('content', 'Pages'),
                    'url' => ['/admin/content/pages/index'],
                ],
                [
                    'icon' => '<i class="fa fa-file-text-o"></i>',
                    'label' => \Yii::t('content', 'Articles'),
                    'url' => ['/admin/content/article/index'],
                ],
                [
                    'icon' => '<i class="fa fa-folder-o"></i>',
                    'label' => \Yii::t('content', 'Categories'),
                    'url' => ['/admin/content/category/index'],
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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
