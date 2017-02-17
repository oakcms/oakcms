<?php

namespace app\modules\importmebel;


/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

class Module extends \app\components\module\Module implements \app\components\module\ModuleEventsInterface
{

    /** @var \app\components\UrlManager The rules to be used in Backend Url management. */
    public static $urlRulesBackend = [
        '/admin/text/default/<_a:[\w\-]+>/<id:\d+>'                => '/admin/text/default/<_a>',
        '/admin/text/default/<_a:[\w\-]+>/<id:\d+>/<file:[\w\-]+>' => '/admin/text/default/<_a>',
        '/admin/text/default/<_a:[\w\-]+>/<file:[\w\-]+>'          => '/admin/text/default/<_a>',
    ];

    /**
     * @param $event \app\modules\admin\widgets\events\MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['importmebel'] = [
            'label' => \Yii::t('importmebel', 'Import Mebel'),
            'icon'  => '<i class="fa fa-upload"></i>',
            'url'   => ['/admin/importmebel'],
        ];
    }

    public function events()
    {
        // TODO: Implement events() method.

        return [
            \app\modules\admin\widgets\Menu::EVENT_FETCH_ITEMS => 'addAdminMenuItem',
        ];

    }
}
