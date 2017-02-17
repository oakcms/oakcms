<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\menu\behaviors;


use yii\base\InvalidConfigException;

/**
 * Class MenuRouterUrlRule
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
abstract class MenuRouterUrlRule extends \yii\base\Object
{
    /**
     * @var string
     */
    public $handler;
    /**
     * @var string
     */
    public $router;

    public function init()
    {
        if (!isset($this->handler) || !is_string($this->handler)) {
            throw new InvalidConfigException(__CLASS__ . '::handler must be set.');
        }

        if (!isset($this->router) || !is_string($this->router)) {
            throw new InvalidConfigException(__CLASS__ . '::router must be set.');
        }
    }

    /**
    * /**
     * @param $requestInfo MenuRequestInfo
     * @param $menuUrlRule MenuUrlRule
     * @return array|false
     */
    public function process($requestInfo, $menuUrlRule)
    {
        return false;
    }
}
