<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: Bootstrap.php
 */

namespace app\modules\admin;

use Yii;
use app\components\View;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        /**
         *
         * @var Module $adminModule
         * @var \app\modules\user\Module $userModule
         *
         */

        if (
            ($app->hasModule('user') && ($userModule = $app->getModule('user')) instanceof \app\modules\user\Module) &&
            ($app->hasModule('admin') && ($adminModule = $app->getModule('admin')) instanceof Module)
        ) {
            Yii::$container->set('yii\web\User', [
                'enableAutoLogin' => true,
                'loginUrl'        => ['/admin/default/login'],
                'identityClass'   => $userModule->modelMap['User'],
            ]);

            $configUrlRule = [
                'prefix' => $adminModule->urlPrefix,
                'rules'  => $adminModule->urlRules,
            ];

            if ($adminModule->urlPrefix != 'admin') {
                $configUrlRule['routePrefix'] = 'admin';
            }

            $configUrlRule['class'] = 'yii\web\GroupUrlRule';
            $rule = Yii::createObject($configUrlRule);

            $app->urlManager->addRules([$rule], false);
        }
    }
}