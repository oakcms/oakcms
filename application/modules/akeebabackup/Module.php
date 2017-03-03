<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\akeebabackup;

use app\components\module\ModuleEventsInterface;
use app\modules\admin\widgets\events\MenuItemsEvent;
use app\modules\admin\widgets\Menu;
use app\modules\akeebabackup\helpers\AkeebaBackupYii;
use app\modules\admin\Module as AdminModule;
use yii\base\Event;

class Module extends \app\components\module\Module implements ModuleEventsInterface
{

    public function init()
    {
        parent::init();


        $this->boot();

        Event::on(AdminModule::className(), AdminModule::EVENT_MODULE_AFTER_ACTIVATION, function () {});
    }

    public function boot() {
        AkeebaBackupYii::$dirName = 'akeebabackup';
        AkeebaBackupYii::$fileName = basename(__FILE__);
        AkeebaBackupYii::$absoluteFileName = __FILE__;
        AkeebaBackupYii::$wrongPHP = version_compare(PHP_VERSION, AkeebaBackupYii::$minimumPHP, 'lt');

        $aksolowpPath = dirname(__FILE__);
        define('AKEEBA_SOLOYII_PATH', $aksolowpPath);
    }

    /**
     * @param $event MenuItemsEvent
     */
    public function addAdminMenuItem($event)
    {
        $event->items['akeebabackup'] = [
            'label' => \Yii::t('menu', 'Akeeba Backup'),
            'icon' => '<i class="fa fa-bars"></i>',
            'url' => ['/admin/akeebabackup']
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
