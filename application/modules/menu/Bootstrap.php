<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\menu;


use yii\base\BootstrapInterface;
use app\modules\menu\behaviors\MenuManager;
use app\modules\menu\models\MenuItem;
use app\modules\system\models\DbState;
use yii\base\Event;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        Event::on(\app\modules\admin\Bootstrap::class, \app\modules\admin\Bootstrap::EVENT_AFTER_BOOTSTRAP, function () use ($app) {
            \Yii::$container->set('app\modules\menu\behaviors\MenuMap', [
                'cache'           => $app->cache,
                'cacheDependency' => DbState::dependency(MenuItem::tableName()),
            ]);

            \Yii::$container->set('app\modules\menu\behaviors\MenuUrlRule', [
                'cache'           => $app->cache,
                'cacheDependency' => DbState::dependency(MenuItem::tableName()),
            ]);

            $app->set('menuManager', \Yii::createObject(MenuManager::className()));
        });
    }
}
