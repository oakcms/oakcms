<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\language;
use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;

/**
 * language module definition class
 */
class Module extends \app\components\module\Module implements ModuleEventsInterface
{

    public $settings = [];

    public static $urlRulesBackend = [
        'language/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:[\w\-]+>' => 'language/<_c>/<_a>',
    ];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['language'] = [
            'label' => \Yii::t('language', 'Multilingual'),
            'icon'  => '<i class="fa fa-flag"></i>',
            'items' => [
                ['label' => \Yii::t('language', 'Language'), 'url' => ['/admin/language/language/index'], 'icon'  => '<i class="fa fa-flag"></i>'],
//                [
//                    'label' => \Yii::t('language', 'Text'),
//                    'url' => ['/admin/language/source/index'],
//                    'icon'  => '<i class="fa fa-font"></i>'
//                ],
//                [
//                    'label' => \Yii::t('language', 'Translation'),
//                    'url' => ['/admin/language/translate/index'],
//                    'icon'  => '<i class="fa fa-flag-checkered"></i>'
//                ],
            ],
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
