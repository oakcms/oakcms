<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\menu\behaviors;


/**
 * Class MenuRouterUrlRuleParse
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterUrlRuleParse extends MenuRouterUrlRule
{
    /**
     * @var string
     */
    public $menuRoute;

    /**
     * @inheritdoc
     */
    public function process($requestInfo, $menuUrlRule)
    {
        if ($this->menuRoute && $this->menuRoute != $requestInfo->menuRoute) {
            return false;
        }

        return $menuUrlRule->getRouter($this->router)->{$this->handler}($requestInfo);
    }
}
