<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\components\module;


use yii\base\Object;

/**
 * Class Event
 * Класс представляющие модульное событие. Класс сделан абстрактным чтобы принуждать разработчика
 * писать классы событий под каждое событие, таким образом будет сразу понятно какие события представляет модуль,
 * а также в каждый класс события можно будет детально задокументировать
 * @package yii2-module-query
 * @author Gayazov Roman <gromver5@gmail.com>
 */
abstract class Event extends Object {
    /**
     * @var string the event name. This property is set by [[Component::trigger()]] and [[trigger()]].
     * Event handlers may use this property to check what event it is handling.
     */
    public $name;
    /**
     * @var object the sender of this event.
     */
    public $sender;
    /**
     * @var boolean whether the event is handled. Defaults to false.
     * When a handler sets this to be true, the event processing will stop and
     * ignore the rest of the uninvoked event handlers.
     */
    public $handled = false;
}
