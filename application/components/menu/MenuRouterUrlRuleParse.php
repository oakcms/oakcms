<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\components\menu;


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
