<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\components\menu;

/**
 * Class MenuRouterUrlRuleCreate
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterUrlRuleCreate extends MenuRouterUrlRule
{
    /**
     * @var string
     */
    public $requestRoute;
    /**
     * @var array
     */
    public $requestParams;

    /**
     * @inheritdoc
     */
    public function process($requestInfo, $menuUrlRule)
    {
        if ($this->requestRoute && $this->requestRoute != strtok($requestInfo->requestRoute, '?')) {
            return false;
        }
        if (isset($this->requestParams)) {
            foreach ($this->requestParams as $param) {
                if (!isset($requestInfo->requestParams[$param])) {
                    return false;
                }
            }
        }

        return $menuUrlRule->getRouter($this->router)->{$this->handler}($requestInfo);
    }
}
