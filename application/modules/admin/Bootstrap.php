<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\admin;

use app\modules\admin\models\Modules;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\helpers\Url;

class Bootstrap implements BootstrapInterface
{
    const EVENT_AFTER_BOOTSTRAP = 'afterBootstrap';

    /**
     * @var null|\yii\caching\Dependency
     */
    private $_modulesConfigDependency;
    private $_modulesHash;

    public $backendUrlRules = [];
    public $frontendUrlRules = [];
    public $setAppComponents = [];

    public function bootstrap($app)
    {
        if (!$app->hasModule('admin')) {
            $app->setModule('admin', [
                'class' => 'app\modules\admin\Module'
            ]);
        }

        /**
         * @var $adminModule Module
         */
        $adminModule = $app->getModule('admin');

        /**
         * автоматична загрузка модулів
         */
        $adminModule->activeModules = Modules::findAllActiveAdmin();

        $modules_backend = [];
        $modules_frontend = [];
        $modules_console = [];

        foreach ($adminModule->activeModules as $name => $module) {
            if (class_exists($module->class)) {
                if (!Yii::$app->request->isConsoleRequest) {
                    if ($module->isAdmin) {
                        $class = new \ReflectionClass($module->class);

                        $modules_backend[$name]['class'] = $module->class;

                        if (
                            $class->hasProperty('controllerNamespace') &&
                            $class->getStaticPropertyValue('controllerNamespace', '') != ''
                        ) {
                            $modules_backend[$name]['controllerNamespace'] =
                                $class->getStaticPropertyValue('controllerNamespace');
                        } else {
                            $modules_backend[$name]['controllerNamespace'] =
                                'app\modules\\' . $module->name . '\controllers\backend';
                        }

                        if (
                            $class->hasProperty('viewPath') &&
                            $class->getStaticPropertyValue('viewPath', '') != ''
                        ) {
                            $modules_backend[$name]['viewPath'] = $class->getStaticPropertyValue('viewPath');
                        } else {
                            $modules_backend[$name]['viewPath'] = '@app/modules/' . $module->name . '/views/backend';
                        }

                        if ($class->hasProperty('urlRulesBackend')) {
                            foreach ($class->getStaticPropertyValue('urlRulesBackend') as $k => $item) {
                                $this->backendUrlRules[$k] = $item;
                            }
                        }

                        if ($class->hasProperty('urlRulesFrontend')) {
                            foreach ($class->getStaticPropertyValue('urlRulesFrontend') as $k => $item) {
                                $this->frontendUrlRules[$k] = $item;
                            }
                        }

                        if ($class->hasProperty('setAppComponents')) {
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
                        $modules_frontend[$name]['controllerNamespace'] = 'app\modules\\' . $module->name .
                            '\controllers\frontend';
                        $modules_frontend[$name]['viewPath'] = '@app/modules/' . $module->name .
                            '/views/frontend';

                        if (isset($module->settings) AND is_array($module->settings)) {
                            $modules_frontend[$name]['settings'] = $module->settings;
                        }
                    }
                } else {
                    $modules_console[$name]['class'] = $module->class;
                    $modules_console[$name]['controllerNamespace'] = 'app\modules\\' . $module->name .
                        '\controllers\console';
                }
            }

            // Bootstrap
            if (class_exists($module->bootstrapClass)) {
                $component = Yii::createObject($module->bootstrapClass);

                if ($component instanceof BootstrapInterface) {
                    Yii::trace('Bootstrap with ' . get_class($component) . '::bootstrap()', __METHOD__);
                    $component->bootstrap($app);
                } else {
                    Yii::trace('Bootstrap with ' . get_class($component), __METHOD__);
                }
            }
        }

        Yii::$app->setComponents($this->setAppComponents);
        Yii::$app->setModules($modules_frontend);
        Yii::$app->setModules($modules_console);

        $adminModule->setModules($modules_backend);

        $this->_modulesHash = md5(json_encode([$modules_frontend, $modules_backend]));

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
        $app->getUrlManager()->addRules([$rule], false);


        if (isset($this->backendUrlRules)) {
            $app->getUrlManager()->addRules($this->backendUrlRules, false);
        }

        if (isset($this->frontendUrlRules)) {
            $app->getUrlManager()->addRules($this->frontendUrlRules, false);
        }

        if (!Yii::$app->request->isConsoleRequest) {
            $rHostInfo = Url::home(true);

            if (
                !Yii::$app->user->isGuest &&
                strpos(Yii::$app->request->absoluteUrl, $rHostInfo . 'admin') === false &&
                strpos(Yii::$app->request->absoluteUrl, $rHostInfo . 'gii') === false &&
                strpos(Yii::$app->request->absoluteUrl, $rHostInfo . 'debug') === false &&
                !Yii::$app->request->isAjax &&
                Yii::$app->getView()->adminPanel
            ) {
                $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
                    Yii::$app->getView()->bodyClass[] = 'oak-admin-bar';
                    $app->getView()->on(\yii\web\View::EVENT_BEGIN_BODY, [$this, 'renderToolbar']);
                });
            }
        }

        Yii::$container->set('app\components\module\ModuleQuery', [
            'cache' => $app->cache,
            'cacheDependency' => $this->_modulesConfigDependency
        ]);

        Event::trigger(self::class, self::EVENT_AFTER_BOOTSTRAP);

    }

    public function renderToolbar()
    {
        $view = Yii::$app->getView();
        echo $view->render('@app/templates/backend/base/modules/admin/views/layouts/blocks/admin_bar.php');
    }

    /**
     * @return string
     */
    public function getModulesHash() {
        return $this->_modulesHash;
    }

}
