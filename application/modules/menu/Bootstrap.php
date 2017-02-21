<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\menu;


use yii\base\BootstrapInterface;
use app\modules\menu\behaviors\MenuManager;
use app\modules\menu\models\MenuItem;
use app\modules\system\models\DbState;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        \Yii::$container->set('app\modules\menu\behaviors\MenuMap', [
            'cache'           => $app->cache,
            'cacheDependency' => DbState::dependency(MenuItem::tableName()),
        ]);

        \Yii::$container->set('app\modules\menu\behaviors\MenuUrlRule', [
            'cache'           => $app->cache,
            'cacheDependency' => DbState::dependency(MenuItem::tableName()),
        ]);

        $app->set('menuManager', \Yii::createObject(MenuManager::className()));
    }
}
