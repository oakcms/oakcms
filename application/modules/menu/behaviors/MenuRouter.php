<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\menu\behaviors;


/**
 * Class MenuRouter
 * Базовый класс для описания правил маршрутизации
 * используется на уровне модулей в связке с \app\modules\menu\behaviors\interfaces\module\MenuRouterInterface
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouter extends \yii\base\Object
{
    /**
     * @return array
     */
    public function createUrlRules()
    {
        return [];
    }

    /**
     * @return array
     */
    public function parseUrlRules()
    {
        return [];
    }
}
