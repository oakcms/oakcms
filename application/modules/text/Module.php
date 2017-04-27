<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\text;

use app\components\module\ModuleEventsInterface;
use app\components\UrlManager;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;

class Module extends \app\components\module\Module implements ModuleEventsInterface
{

    /** @var UrlManager The rules to be used in Backend Url management. */
    public static $urlRulesBackend = [
        '/admin/text/default/<_a:[\w\-]+>/<id:\d+>'                => '/admin/text/default/<_a>',
        '/admin/text/default/<_a:[\w\-]+>/<id:\d+>/<file:[\w\-]+>' => '/admin/text/default/<_a>',
        '/admin/text/default/<_a:[\w\-]+>/<file:[\w\-]+>'          => '/admin/text/default/<_a>',
    ];

    public $settings = [];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['text'] = [
            'label' => \Yii::t('text', 'Custom Blocks'),
            'icon'  => '<i class="fa fa-font"></i>',
            'url'   => ['/admin/text'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Menu::EVENT_FETCH_ITEMS => 'addAdminMenuItem',
        ];
    }
}
