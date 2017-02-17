<?php

/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

class Module extends \app\components\module\Module
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
        $event->items['text'] = [
            'label' => \Yii::t('text', 'Text Block'),
            'icon'  => '<i class="fa fa-font"></i>',
            'url'   => ['/admin/text'],
        ];
    }
}
