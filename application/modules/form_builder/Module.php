<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;

/**
 * form_builder module definition class
 */
class Module extends \app\components\module\Module implements ModuleEventsInterface
{
    public $settings = [];

    public static $htmlFormSuccess = '';

    public static $urlRulesFrontend = [
        'form_builder/<slug:[\w\-]+>' => 'form_builder/form/view',
    ];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['form_builder'] = [
            'label' => \Yii::t('form_builder', 'Form Builder'),
            'icon' => '<span class="ion-paintbrush"></span>',
            'items' => [
                [
                    'label' => \Yii::t('form_builder', 'Forms'),
                    'icon' => '<span class="ion-filing"></span>',
                    'url' => ['/admin/form_builder/forms/index']
                ],
                [
                    'label' => \Yii::t('form_builder', 'Submissions'),
                    'icon' => '<span class="ion-email"></span>',
                    'url' => ['/admin/form_builder/submissions/index']
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
}
