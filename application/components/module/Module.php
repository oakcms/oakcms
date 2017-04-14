<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\components\module;

use app\modules\admin\models\Modules;

class Module extends \yii\base\Module
{

    /** @var array  */
    public $settings = [];

    /** @var array The rules to be used in Backend Url management. */
    public static $urlRulesBackend = [];

    /** @var array The rules to be used in Frontend Url management. */
    public static $urlRulesFrontend = [];

    /**
     * Module name getter
     *
     * @param $namespace
     * @return string|bool
     */
    public static function getModuleName($namespace)
    {
        foreach (Modules::findAllActive() as $module)
        {
            $moduleClassPath = preg_replace('/[\w]+$/', '', $module->class);
            if(strpos($namespace, $moduleClassPath) !== false){
                return $module->name;
            }
        }
        return false;
    }
}
