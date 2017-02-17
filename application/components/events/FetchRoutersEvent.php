<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\components\events;


use app\components\module\Event;

/**
 * Class FetchRoutersEvent
 * Сбор маршрутизаторов с модулей
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \app\modules\menu\behaviors\MenuUrlRule $sender
 */
class FetchRoutersEvent extends Event {
    /**
     * @var array   ["RouterClass1", "RouterClass2", ...]
     */
    public $routers;
}
