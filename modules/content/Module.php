<?php

namespace app\modules\content;

/**
 * content module definition class
 */
class Module extends \yii\base\Module
{
    public $settings = [
        'show_title'            => false,
        'link_titles'           => false,
        'show_intro'            => false,
        'show_category'         => false,
        'link_category'         => false,
        'show_parent_category'  => false,
        'link_parent_category'  => false,
        'show_author'           => false,
        'link_author'           => false,
        'show_create_date'      => false,
        'show_modify_date'      => false,
        'show_publish_date'     => false,
        'show_hits'             => false,
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
                    'url' => ['/admin/content'],
                ],
                [
                    'icon' => '<i class="fa fa-folder-o"></i>',
                    'label' => \Yii::t('content', 'Categories'),
                    'url' => ['/admin/category'],
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
