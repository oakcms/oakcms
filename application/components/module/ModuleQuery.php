<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\components\module;

use Yii;
use yii\caching\Cache;

/**
 * Class ModuleQuery
 * Не использовать ModuleQuery в init() и __construct() методах модулей! Это может зациклить приложение
 * @package yii2-module-query
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ModuleQuery extends \yii\base\Object
{
    //методы слияния результатов функции fetch
    const AGGREGATE_MERGE = 1;    //слияние с использованием array_merge, применимо если все результаты являются массивами
    const AGGREGATE_PUSH = 2;     //добавление результатов Push методом

    /**
     * @var Cache
     */
    public $cache;
    public $cacheDuration;
    public $cacheDependency;

    /**
     * @var Module корневой модуль с которого начинать поиск
     */
    private $_nodeOf;
    /**
     * @var integer глубина поиска
     */
    private $_depth;
    /**
     * @var string имя интерфейса, класса или трейта которое должно наследоваться искомыми модулями
     */
    private $_implement;
    /**
     * @var string имя события
     */
    private $_event;
    /**
     * @var string название своиства модуля по которому будет производится сортировка
     */
    private $_orderBy;
    /**
     * @var integer SORT_ASC | SORT_DESC порядок сортировки
     */
    private $_sort;

    /**
     * @return static
     */
    public static function instance()
    {
        return Yii::createObject(get_called_class());
    }


    public function nodeOf(Module $module)
    {
        $this->_nodeOf = $module;

        return $this;
    }

    public function depth($value)
    {
        $this->_depth = $value;

        return $this;
    }

    public function implement($className)
    {
        $this->_implement = $className;

        return $this;
    }

    public function orderBy($orderBy, $sort = SORT_ASC)
    {
        $this->_orderBy = $orderBy;
        $this->_sort = $sort;

        return $this;
    }

    /**
     * @param Cache $cache
     * @return $this
     */
    public function cache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function cacheDuration($cacheDuration)
    {
        $this->cacheDuration = $cacheDuration;

        return $this;
    }

    public function cacheDependency($cacheDependency)
    {
        $this->cacheDependency = $cacheDependency;

        return $this;
    }

    /**
     * Запускает метод $method в модулях удовлетворяющих условиям запроса
     * @param string $method название метода
     * @param array $params параметры передаваемые в метод
     */
    public function invoke($method, $params = [])
    {
        foreach ($this->find() as $moduleId) {
            $module = Yii::$app->getModule($moduleId);
            call_user_func_array([$module, $method], $params);
        }
    }

    /**
     * Аналогично [[self::invoke]] только с возможностью обработчиком события прервать его
     * @param string $name Название события
     * @param Event $event Объект события
     * @return mixed
     */
    public function trigger($name, Event $event)
    {
        $this->_event = $name;

        foreach ($this->find() as $moduleId) {
            /** @var ModuleEventsInterface $module */
            $module = Yii::$app->getModule($moduleId);

            $func = $module->events()[$name];
            if (is_string($func) && strpos($func, '::') === false) {
                $func = [$module, $func];
            }

            call_user_func($func, $event);

            if ($event->handled === true) {
                return;
            }
        }
    }

    /**
     * @param string $method название метода
     * @param array $params параметры передаваемые в метод
     * @param int $aggregate метод слияния результатов выполнения функций $method
     * @param bool $useCache применять ли кеширование к [[self::fetchResults]] фазе
     * @return array|mixed
     */
    public function fetch($method, $params = [], $aggregate = self::AGGREGATE_PUSH, $useCache = true)
    {
        if ($useCache && $this->cache) {
            $cacheKey = [$this->getFindCacheKey(), $method, $params, $aggregate];
            if (($result = $this->cache->get($cacheKey)) === false) {
                $result = $this->fetchResults($this->find(), $method, $params, $aggregate);
                $this->cache->set($cacheKey, $result, $this->cacheDuration, $this->cacheDependency);
            }

            return $result;
        }

        return $this->fetchResults($this->find(), $method, $params, $aggregate);
    }

    /**
     * @param array $modules
     * @param string $method
     * @param array $params
     * @param int $aggregate
     * @return array
     */
    private function fetchResults($modules, $method, $params, $aggregate = self::AGGREGATE_PUSH)
    {
        $result = [];
        foreach ($modules as $moduleId) {
            $module = Yii::$app->getModule($moduleId);
            if ($aggregate === self::AGGREGATE_MERGE) {
                $result = array_merge($result, call_user_func_array([$module, $method], $params));
            } else {
                $result[] = call_user_func_array([$module, $method], $params);
            }
        }

        return $result;
    }

    /**
     * Производит поиск модулей удовлетворяющих условиям запроса
     * @return string[] Modules Ids
     */
    public function find()
    {
        if ($this->cache) {
            $cacheKey = $this->getFindCacheKey();
            if (($result = $this->cache->get($cacheKey)) === false) {
                $modules = $this->findModules($this->_nodeOf ? $this->_nodeOf : Yii::$app);
                if ($this->_orderBy) {
                    usort($modules, [$this, 'compareModules']);
                }
                $result = $this->extractModuleIds($modules);
                $this->cache->set($cacheKey, $result, $this->cacheDuration, $this->cacheDependency);
            }

            return $result;
        }

        $modules = $this->findModules($this->_nodeOf ? $this->_nodeOf : Yii::$app);
        if ($this->_orderBy) {
            usort($modules, [$this, 'compareModules']);
        }
        return $this->extractModuleIds($modules);
    }

    private function getFindCacheKey()
    {
        return [__CLASS__, Yii::$app->name, $this->_implement, $this->_event, $this->_orderBy, $this->_sort];
    }

    /**
     * @param $parentModule
     * @param int $level
     * @return \yii\base\Module[]
     */
    private function findModules($parentModule, $level = 1)
    {
        /** @var Module[] $modules */
        $modules = [];
        if (isset($this->_depth) && $this->_depth < $level) {
            return $modules;
        }

        foreach ($parentModule->getModules() as $name => $config) {
            $module = $parentModule->getModule($name);
            $matched = true;

            if (isset($this->_implement) && !is_a($module, $this->_implement)) {
                $matched = false;
            } elseif (isset($this->_event) && (!$module instanceof ModuleEventsInterface || !array_key_exists($this->_event, $module->events()))) {
                $matched = false;
            }
            if ($matched) $modules[] = $module;
            $modules = array_merge($modules, $this->findModules($module, $level + 1));
        }

        return $modules;
    }

    /**
     * @param $a Module
     * @param $b Module
     * @return int
     */
    protected function compareModules($a, $b)
    {
        $aOrder = $a->canGetProperty($this->_orderBy) ? $a->{$this->_orderBy} : null;
        $bOrder = $b->canGetProperty($this->_orderBy) ? $b->{$this->_orderBy} : null;

        if ($bOrder === null) {
            return -1;
        }

        if ($aOrder === null) {
            return 1;
        }

        $result = $aOrder < $bOrder ? -1 : 1;
        return $this->_sort === SORT_DESC ? -1 * $result : $result;
    }

    /**
     * @param $modules
     * @return string[] Modules Ids
     */
    protected function extractModuleIds(&$modules)
    {
        return array_map(function($module) {
            /** @var Module $module */
            return $module->getUniqueId();
        }, $modules);
    }
}
