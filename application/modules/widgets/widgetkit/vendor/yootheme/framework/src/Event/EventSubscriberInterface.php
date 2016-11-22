<?php

namespace YOOtheme\Framework\Event;

interface EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents();
}
