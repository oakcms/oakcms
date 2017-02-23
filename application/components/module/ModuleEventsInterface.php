<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\components\module;


/**
 * Interface ModuleEventsInterface
 * @package yii2-module-query
 * @author Gayazov Roman <gromver5@gmail.com>
 */
interface ModuleEventsInterface
{
    /**
     * @return array [eventName => callable, ...]
     * callable:
     *  - 'funcName', $module->funcName
     *  - 'Class::funcName', Class::funcName
     *  - ['Class', 'funcName'], Class::funcName
     *  - [Object, 'funcName'], Object->funcName
     */
    public function events();
}
