<?php
namespace app\components;

use app\components\module\Module;
use yii\base\ExitException;
use yii\web\HttpException;

/**
 * Base API component. Used by all modules
 * @package oakcms
 */
class API extends \yii\base\Object
{
    /** @var array */
    static private $classes;

    /** @var string module name */
    public $module;

    /** @var string app language */
    public $language;


    public function init()
    {
        parent::init();

        if(!($this->module = Module::getModuleName(self::className()))) {
            throw new HttpException(500, \Yii::t('system', 'The module can not be found'));
        }

        $this->language || $this->language = \Yii::$app->language;

    }

    public static function __callStatic($method, $params)
    {
        $name = (new \ReflectionClass(self::className()))->getShortName();
        if (!isset(self::$classes[$name])) {
            self::$classes[$name] = new static();
        }

        return call_user_func_array([self::$classes[$name], 'api_' . $method], $params);
    }

    /**
     * Wrap text with liveEdit tags, which later will fetched by jquery widget
     *
     * @param        $text
     * @param        $path
     * @param string $tag
     *
     * @return string
     */
    public static function liveEdit($text, $path, $tag = 'span')
    {
        return $text ? '<' . $tag . ' class="oakcms-edit" data-edit="' . $path . '">' . $text . '</' . $tag . '>' : '';
    }
}
