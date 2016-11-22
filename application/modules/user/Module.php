<?php

namespace app\modules\user;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;

class Module extends \yii\base\Module implements ModuleEventsInterface
{

    /**
     * @var string
     */
    public $defaultRole = 'user';

    /**
     * @var int
     */
    public $emailConfirmTokenExpire = 259200; // 3 days

    /**
     * @var int
     */
    public $passwordResetTokenExpire = 3600;

    /**
     * @var array
    */
    public $settings = [
        'defaultRole' => [
            'type' => 'textInput',
            'value' => 'user'
        ],
        'emailConfirmTokenExpire' => [
            'type' => 'textInput',
            'value' => '259200' // 3 days
        ],
        'passwordResetTokenExpire' => [
            'type' => 'textInput',
            'value' => '3600'
        ]
    ];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['user'] = [
            'label' => \Yii::t('user', 'User'),
            'icon' => '<i class="fa fa-user"></i>',
            'url' => ['/admin/user']
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
        return parent::init();


    }
}
