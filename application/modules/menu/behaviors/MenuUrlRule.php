<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\menu\behaviors;


use app\components\module\ModuleEvent;
use app\components\UrlManager;
use app\modules\menu\models\MenuItem;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\caching\Cache;
use yii\di\Instance;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\UrlRuleInterface;
use yii\web\View;

/**
 * Class MenuUrlRule
 * Маршрутизация меню
 * для маршрутизации используется объекты MenuRouter с правилами маршрутизации,
 * MenuRouter* объект для приложения может расшарить любой модуль, через
 * \app\modules\menu\interfaces\module\MenuRouterInterface
 * @package oakcms
 */
class MenuUrlRule extends Object implements UrlRuleInterface
{
    const EVENT_FETCH_MODULE_ROUTERS = 'MenuUrlRuleRouters';

    public $cache = 'cache';
    public $cacheDuration;
    public $cacheDependency;
    /**
     * @var MenuManager
     */
    public $menuManager;

    private $_routers = [];
    /**
     * @var MenuRouterUrlRuleCreate[]
     */
    private $_createUrlRules = [];
    /**
     * @var MenuRouterUrlRuleParse[]
     */
    private $_parseUrlRules = [];
    /**
     * @var array
     */
    private $_metaData;

    /**
     * @inheritdoc
     */
    public function init()
    {
        Instance::ensure($this->menuManager, MenuManager::className());

        if ($this->cache) {
            /** @var Cache $cache */
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheKey = __CLASS__;
            if ((list($createUrlRules, $parseUrlRules) = $this->cache->get($cacheKey)) === false) {
                $this->buildRules();
                $this->cache->set($cacheKey, [$this->_createUrlRules, $this->_parseUrlRules], $this->cacheDuration, $this->cacheDependency);
            } else {
                $this->_createUrlRules = $createUrlRules;
                $this->_parseUrlRules = $parseUrlRules;
            }
        } else {
            $this->buildRules();
        }
    }

    /**
     * Собирает правила маршрутизации со всех маршрутизаторов модулей
     * @throws InvalidConfigException
     */
    protected function buildRules()
    {

        //нам нужно собрать все роутеры от модулей и вытащить из них инструкции по маршрутизации
        $routers = ModuleEvent::trigger(self::EVENT_FETCH_MODULE_ROUTERS, new \app\components\events\FetchRoutersEvent([
            'routers' => [],
            'sender'  => $this,
        ]), 'routers');


        // вытаскиваем инструкции из всех роутеров
        foreach ($routers as $routerClass) {
            $router = $this->getRouter($routerClass);
            foreach ($router->createUrlRules() as $rule) {
                @$rule['class'] or $rule['class'] = MenuRouterUrlRuleCreate::className();
                $rule['router'] = $router->className();
                $this->_createUrlRules[] = Yii::createObject($rule);
            }

            foreach ($router->parseUrlRules() as $rule) {
                @$rule['class'] or $rule['class'] = MenuRouterUrlRuleParse::className();
                $rule['router'] = $router->className();
                $this->_parseUrlRules[] = Yii::createObject($rule);
            }
        }
    }

    /**
     * @param $router string|array|MenuRouter
     *
     * @return MenuRouter
     * @throws InvalidConfigException
     */
    public function getRouter($router)
    {
        if (is_string($router) && isset($this->_routers[$router])) {
            return $this->_routers[$router];
        }

        if (!is_object($router)) {
            $router = Yii::createObject($router);
        }

        if (!$router instanceof MenuRouter) {
            throw new InvalidConfigException('MenuItemRoutes must be an instance of \app\modules\menu\behaviors\components\MenuRouter class.');
        }

        return $this->_routers[$router->className()] = $router;
    }

    /**
     * Parses the given request and returns the corresponding route and parameters.
     *
     * @param \yii\web\UrlManager $manager the URL manager
     * @param Request             $request the request component
     *
     * @return array|bool the parsing result. The route and the parameters are returned as an array.
     * @throws ForbiddenHttpException
     */
    public function parseRequest($manager, $request)
    {
        $menuMap = $this->menuManager->getMenuMap();
        if (!($pathInfo = $request->getPathInfo() or $pathInfo = ($mainMenu = $menuMap->getMainMenu()) ? $mainMenu->path : null)) {
            return false;
        }

        // помечаем как активные все пункты меню которые ссылаются на тотже урл что в запросе
        $this->menuManager->setActiveMenuIds($menuMap->getMenuIdsByLink($request->getUrl()));

        // ищем пункт меню, чей путь совпадает с путем в запросе
        if ($menu = $menuMap->getMenuByPathRecursive($pathInfo)) {
            // определяем в каком контексте ("Точный" или "Подходящий") рассматривать активное меню
            $menu->setContext($menu->path === $pathInfo ? MenuItem::CONTEXT_PROPER : MenuItem::CONTEXT_APPLICABLE);
            // устанавливаем найденный пункт меню в качестве активного
            $this->menuManager->setActiveMenu($menu);
            // добавляем данный пункт в список активных пунктов меню
            $this->menuManager->addActiveMenuId($menu->id);
            if ($menu->getContext() === MenuItem::CONTEXT_PROPER) {
                //при "точном" совпадении, метаданные меню перекрывают метаднные контроллера
                Yii::$app->getView()->on(View::EVENT_BEGIN_PAGE, [$this, 'applyMetaData'], null, false);
            } else {
                //при "подходящем" устанавливаются по умолчанию
                $this->applyMetaData();
            }
        } else {
            return false;
        }

        // устанавливаем макет приложению
        Event::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, [$this, 'applyLayout']);

        // Проверка на доступ к пунтку меню
        if (!empty($menu->access_rule) && !Yii::$app->user->can($menu->access_rule)) {
            if (Yii::$app->user->getIsGuest()) {
                Yii::$app->user->loginRequired();
            } else {
                throw new ForbiddenHttpException(Yii::t('menu', 'You have no rights for access to this section of the site.'));
            }
        }

        if ($menu->getContext() === MenuItem::CONTEXT_PROPER) {
            // при "точном" контексте пункта меню, возвращаем роут на компонент
            return $menu->parseUrl();
        } else {
            /*
             * при "подходящем" контексте пункта меню, необходимо на основании оставшейся части пути
             * и информации из пункта меню маршрутизировать приложение
             */
            $requestRoute = substr($pathInfo, mb_strlen($menu->path) + 1);
            list($menuRoute, $menuParams) = $menu->parseUrl();

            $requestInfo = new MenuRequestInfo([
                'menuMap'       => $menuMap,
                'menuRoute'     => $menuRoute,
                'menuParams'    => $menuParams,
                'requestRoute'  => $requestRoute,
                'requestParams' => $request->getQueryParams(),
            ]);

            foreach ($this->_parseUrlRules as $rule) {
                if ($result = $rule->process($requestInfo, $this)) {
                    return $result;
                }
            }

            return false;
        }
    }

    /**
     * Установка метаданных пункта меню (учитываются предки и главное меню)
     */
    public function applyMetaData()
    {
        $metaData = $this->activeMenuMetaData();

        $data = [
            'title' => isset($metaData['title']) ? $metaData['title'] : null,
            'desc' => isset($metaData['description']) ? $metaData['description'] : null,
            'keys' => isset($metaData['keywords']) ? $metaData['keywords'] : null,
            'canonical' => isset($metaData['canonical']) ? $metaData['canonical'] : null,
        ];

        Yii::$app->getView()->setSeoData($data);

        if (!empty($metaData['robots'])) {
            Yii::$app->getView()->registerMetaTag(['name' => 'robots', 'content' => $metaData['robots']], 'robots');
        }
    }

    /**
     * @return array
     */
    protected function activeMenuMetaData()
    {
        if (!isset($this->_metaData)) {
            $menu = $this->menuManager->getActiveMenu();

            if ($this->cache) {
                $cacheKey = [__CLASS__, $menu->id];
                if (($this->_metaData = $this->cache->get($cacheKey)) === false) {
                    $this->_metaData = $this->buildMenuMetaData($menu);
                    $this->cacheDependency = \Yii::createObject([
                        'class' => 'yii\caching\DbDependency',
                        'sql' => 'SELECT MAX(updated_at) FROM '.MenuItem::tableName(),
                    ]);
                    $this->cache->set($cacheKey, $this->_metaData, $this->cacheDuration, $this->cacheDependency);
                }
            } else {
                $this->_metaData = $this->buildMenuMetaData($menu);
            }
        }

        return $this->_metaData;
    }

    /**
     * Мерджит метаданые пункта меню в порядке
     * - Пункт меню, помеченный как главная страница (если есть)
     * - Все предки пункта меню
     * - Пункт меню
     *
     * @param $menu MenuItem
     *
     * @return array
     */
    protected function buildMenuMetaData($menu)
    {
        $chain = [];

        $chain = array_merge($chain, $menu->parents()->excludeRoots()->all(), [$menu]);
        $metaDataChain = array_map(function ($value) {
            /** @var $value MenuItem */
            $metaData = [];

            if (!empty($value->metatitle)) {
                $metaData['title'] = $value->metatitle;
            }
            if (!empty($value->metakey)) {
                $metaData['keywords'] = $value->metakey;
            }
            if (!empty($value->metadesc)) {
                $metaData['description'] = $value->metadesc;
            }
            if (!empty($value->robots)) {
                $metaData['robots'] = $value->robots;
            }
            if (!empty($value->layout_path)) {
                $metaData['layout'] = $value->layout_path;
            }

            return $metaData;
        }, $chain);

        return count($metaDataChain) > 1 ? call_user_func_array('yii\helpers\ArrayHelper::merge', $metaDataChain) : $metaDataChain[0];
    }

    /**
     * Creates a URL according to the given route and parameters.
     *
     * @param UrlManager $manager the URL manager
     * @param string     $route   the route. It should not have slashes at the beginning or the end.
     * @param array      $params  the parameters
     *
     * @return string|boolean the created URL, or false if this rule cannot be used for creating this URL.
     */
    public function createUrl($manager, $route, $params)
    {
        $language = \Yii::$app->language;

        $menuMap = $this->menuManager->getMenuMap($language);
        $route = MenuItem::toRoute($route, $params);

        if ($path = $menuMap->getMenuPathByRoute($route)) {
            if(($menu = $menuMap->getMenuByRoute($route)) && $menu->status == 2) {
                return '';
            }
            return $path;
        }

        $requestInfo = new MenuRequestInfo([
            'menuMap'       => $menuMap,
            'requestRoute'  => $route,
            'requestParams' => $params,
        ]);

        foreach ($this->_createUrlRules as $rule) {
            if ($result = $rule->process($requestInfo, $this)) {
                return $result;
            }
        }

        return false;
    }

    /**
     * Установка шаблона пункта меню (учитываются предки и главное меню)
     */
    public function applyLayout()
    {
        $metaData = $this->activeMenuMetaData();
        if (!empty($metaData['layout'])) {
            Yii::$app->controller->layout = $metaData['layout'];
        }
    }
}
