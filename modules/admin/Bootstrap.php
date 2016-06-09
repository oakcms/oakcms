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
use app\modules\admin\models\ModulesModules;

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

        if ($app->hasModule('admin') && ($adminModule = $app->getModule('admin')) instanceof Module) {

            /**
             * автоматична загрузка модулів
             */
            $adminModule->activeModules = ModulesModules::findAllActiveAdmin();
            $modules_backend = [];
            $modules_frontend = [];
            foreach ($adminModule->activeModules as $name => $module) {
                if($module->isAdmin) {
                    $modules_backend[$name]['class'] = $module->class;
                    $modules_backend[$name]['controllerNamespace'] = 'app\modules\\'.$module->name.'\controllers\backend';
                    $modules_backend[$name]['viewPath'] = '@app/modules/'.$module->name.'/views/backend';

                    if(is_callable([$module->class, 'adminMenu'])) {
                        $adminModule->menuSidebar[] = call_user_func([$module->class, 'adminMenu']);
                    }

                    if (is_array($module->settings)) {
                        $modules_backend[$name]['settings'] = $module->settings;
                    }
                }
                if ($module->isFrontend) {
                    $modules_frontend[$name]['class'] = $module->class;
                    $modules_frontend[$name]['controllerNamespace'] = 'app\modules\\'.$module->name.'\controllers\frontend';
                    $modules_frontend[$name]['viewPath'] = '@app/modules/'.$module->name.'/views/frontend';

                    if (is_array($module->settings)) {
                        $modules_frontend[$name]['settings'] = $module->settings;
                    }
                }
            }

            $adminModule->setModules($modules_backend);
            Yii::$app->setModules($modules_frontend);

            if($app->hasModule('user') && ($userModule = $app->getModule('user')) instanceof \app\modules\user\Module) {
                Yii::$container->set('yii\web\User', [
                    'enableAutoLogin' => true,
                    'loginUrl'        => ['/admin/user/login'],
                    'identityClass'   => $userModule->modelMap['User'],
                ]);
            }

            Yii::setAlias('admin', '@app/modules/admin');

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

            if (!Yii::$app->user->isGuest && strpos(Yii::$app->request->pathInfo, 'admin') === false && strpos(Yii::$app->request->pathInfo, 'gii') === false && strpos(Yii::$app->request->pathInfo, 'treemanager') === false) {
                $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
                    Yii::$app->getView()->bodyClass[] = 'oak-admin-bar';
                    $app->getView()->on(View::EVENT_BEGIN_BODY, [$this, 'renderToolbar']);
                });
            }
        }
    }
    public function renderToolbar()
    {
        $view = Yii::$app->getView();
        echo $view->render('@app/modules/admin/views/layouts/blocks/admin_bar.php');
    }
}
