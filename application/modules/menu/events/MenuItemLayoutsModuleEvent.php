<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\menu\events;
use yii\base\Event;


/**
 * Список шаблонов приложения применимых для пункта меню
 * Class MenuItemLayoutsModuleEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender null
 */
class MenuItemLayoutsModuleEvent extends Event {
    /**
     * @var array   ['@app/views/layouts/main' => 'Main']
     */
    public $items;
}
