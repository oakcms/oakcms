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
use yii\base\Application;
use yii\base\BootstrapInterface;
use app\modules\admin\models\ModulesModules;
use app\components\FrontendView;

class Bootstrap implements BootstrapInterface
{

    public $backendUrlRules = [];
    public $frontendUrlRules = [];
    public $setAppComponents = [];

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
                if(class_exists($module->class)) {
                    if($module->isAdmin) {

                        $modules_backend[$name]['class'] = $module->class;
                        $modules_backend[$name]['controllerNamespace'] = 'app\modules\\'.$module->name.'\controllers\backend';
                        $modules_backend[$name]['viewPath'] = '@app/modules/'.$module->name.'/views/backend';

                        $class = new \ReflectionClass($module->class);
                        if($class->hasProperty('urlRulesBackend')) {
                            foreach ($class->getStaticPropertyValue('urlRulesBackend') as $k => $item) {
                                $this->backendUrlRules[$k] = $item;
                            }
                        }

                        if($class->hasProperty('urlRulesFrontend')) {
                            foreach ($class->getStaticPropertyValue('urlRulesFrontend') as $k => $item) {
                                $this->frontendUrlRules[$k] = $item;
                            }
                        }

                        if($class->hasProperty('setAppComponents')) {
                            foreach ($class->getStaticPropertyValue('setAppComponents') as $k => $item) {
                                $this->setAppComponents[$k] = $item;
                            }
                        }

                        if (isset($module->settings) AND is_array($module->settings)) {
                            $modules_backend[$name]['settings'] = $module->settings;
                        }
                    }
                    if ($module->isFrontend) {
                        $modules_frontend[$name]['class'] = $module->class;
                        $modules_frontend[$name]['controllerNamespace'] = 'app\modules\\'.$module->name.'\controllers\frontend';
                        $modules_frontend[$name]['viewPath'] = '@app/modules/'.$module->name.'/views/frontend';

                        if (isset($module->settings) AND is_array($module->settings)) {
                            $modules_frontend[$name]['settings'] = $module->settings;
                        }
                    }
                }
            }

            Yii::$app->setComponents($this->setAppComponents);
            Yii::$app->setModules($modules_frontend);
            $adminModule->setModules($modules_backend);

            Yii::setAlias('admin', '@app/modules/admin');

            //print_r($adminModule->urlRules + $this->backendUrlRules);
            $configUrlRule = [
                'prefix' => $adminModule->urlPrefix,
                'rules'  => $adminModule->urlRules + $this->backendUrlRules,
            ];

            if ($adminModule->urlPrefix != 'admin') {
                $configUrlRule['routePrefix'] = 'admin';
            }

            $configUrlRule['class'] = 'yii\web\GroupUrlRule';
            $rule = Yii::createObject($configUrlRule);
            $app->urlManager->addRules([$rule], false);


            if(isset($this->backendUrlRules)) {
                $app->urlManager->addRules($this->backendUrlRules, false);
            }

            if(isset($this->frontendUrlRules)) {
                $app->urlManager->addRules($this->frontendUrlRules, false);
            }

            $rHostInfo = Yii::$app->request->hostInfo.DIRECTORY_SEPARATOR;
            if (
                !Yii::$app->user->isGuest &&
                !Yii::$app->request->isAjax &&
                strpos(Yii::$app->request->pathInfo, $rHostInfo.'admin') === false &&
                strpos(Yii::$app->request->pathInfo, $rHostInfo.'gii') === false &&
                strpos(Yii::$app->request->pathInfo, $rHostInfo.'debug') === false
            ) {
                $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
                    Yii::$app->getView()->bodyClass[] = 'oak-admin-bar';
                    $app->getView()->on(FrontendView::EVENT_BEGIN_BODY, [$this, 'renderToolbar']);
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
