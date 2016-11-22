<?php
namespace app\modules\text;

use app\components\module\ModuleEventsInterface;
use app\components\UrlManager;
use app\modules\admin\widgets\Menu;
use app\modules\admin\widgets\events\MenuItemsEvent;

class Module extends \yii\base\Module implements ModuleEventsInterface
{

    /** @var UrlManager The rules to be used in Backend Url management. */
    public static $urlRulesBackend = [
        'text/default/get-settings/<file:[\w\-]+>'      => 'text/default/get-settings',
        'text/default/<_a:[\w\-]+>/<id:\d+>/<file:[\w\-]+>'     => 'text/default/<_a>',
        'text/default/<_a:[\w\-]+>/<file:[\w\-]+>'              => 'text/default/<_a>',
    ];

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['text'] = [
            'label' => \Yii::t('text', 'Text'),
            'icon' => '<i class="fa fa-font"></i>',
            'url' => ['/admin/text'],
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

    public $settings = [];
}
