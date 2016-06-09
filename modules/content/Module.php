<?php

namespace app\modules\content;

use Yii;

/**
 * content module definition class
 */
class Module extends \yii\base\Module
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
    ];

    public function adminMenu() {
        return [
            'label' => \Yii::t('content', 'Content'),
            'icon' => '<i class="fa fa-folder-o"></i>',
            'items' => [
                [
                    'icon' => '<i class="fa fa-file-o"></i>',
                    'label' => \Yii::t('content', 'Pages'),
                    'url' => ['/admin/page'],
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
    public $controllerNamespace = 'app\modules\content\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
