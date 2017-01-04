<?php
namespace app\modules\text;

use app\components\module\ModuleEventsInterface;
use app\components\UrlManager;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;

class Module extends \yii\base\Module implements ModuleEventsInterface
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
            'label' => \Yii::t('text', 'Text'),
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
