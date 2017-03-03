<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 27.06.2016
 * Project: oakcms
 * File name: KeyStorage.php
 */

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Component;

class KeyStorage extends Component
{

    /**
     * @var string
     */
    public $cachePrefix = '_KeyStorage';

    /**
     * @var int
     */
    public $cachingDuration = 60;

    /**
     * @var string
     */
    public $modelClass = 'app\modules\system\models\SystemSettings';

    /**
     * @var array Runtime values cache
     */
    private $values = [];

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $model = $this->getModel($key);
        if (!$model) {
            $model = new $this->modelClass;
            $model->param_name = $key;
        }
        $model->param_value = $value;
        if ($model->save(false)) {
            $this->values[$key] = $value;
            Yii::$app->cache->set($this->getCacheKey($key), $value, $this->cachingDuration);
            return true;
        };
        return false;
    }

    /**
     * @param array $values
     */
    public function setAll(array $values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param $key
     * @param null $default
     * @param bool $cache
     * @param int|bool $cachingDuration
     * @return mixed|null
     */
    public function get($key, $default = null, $cache = true, $cachingDuration = false)
    {
        if ($cache) {
            $cacheKey = $this->getCacheKey($key);
            $value = ArrayHelper::getValue($this->values, $key, false) ?: Yii::$app->cache->get($cacheKey);
            if ($value === false) {
                if ($model = $this->getModel($key)) {
                    $value = $model->param_value;
                    $this->values[$key] = $value;
                    Yii::$app->cache->set(
                        $cacheKey,
                        $value,
                        $cachingDuration === false ? $this->cachingDuration : $cachingDuration
                    );
                } else {
                    $value = $default;
                }
            }
        } else {
            $model = $this->getModel($key);
            $value = $model ? $model->value : $default;
        }
        return $value;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getAll(array $keys)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key);
        }
        return $values;
    }

    /**
     * @param $key
     * @param bool $cache
     * @return bool
     */
    public function has($key, $cache = true)
    {
        return $this->get($key, null, $cache) !== null;
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys)
    {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $key
     * @return bool
     */
    public function remove($key)
    {
        unset($this->values[$key]);
        return call_user_func($this->modelClass.'::deleteAll', ['param_name' => $key]);
    }

    /**
     * @param array $keys
     */
    public function removeAll(array $keys)
    {
        foreach ($keys as $key) {
            $this->remove($key);
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getModel($key)
    {
        $query = call_user_func($this->modelClass.'::find');
        return $query->where(['param_name'=>$key])->select(['param_name', 'param_value'])->one();
    }

    /**
     * @param $key
     * @return array
     */
    protected function getCacheKey($key)
    {
        return [
            __CLASS__,
            $this->cachePrefix,
            $key
        ];
    }

}
