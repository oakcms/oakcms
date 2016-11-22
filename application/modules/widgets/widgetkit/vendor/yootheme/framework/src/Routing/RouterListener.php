<?php

namespace YOOtheme\Framework\Routing;

use YOOtheme\Framework\Event\EventSubscriberInterface;

class RouterListener implements EventSubscriberInterface
{
    public function onRequest($event, $app)
    {
        $request = $event['request'];
        $route   = $app['router']->matchRequest($request);
        $request->attributes->add(array_merge($route->getOptions(), array('_callable' => $route->getCallable())));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request' => 'onRequest'
        );
    }
}
