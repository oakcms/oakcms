<?php

namespace YOOtheme\Widgetkit\Framework\User;

use YOOtheme\Widgetkit\Framework\Event\EventSubscriberInterface;
use YOOtheme\Widgetkit\Framework\Routing\Exception\HttpException;

class AccessListener implements EventSubscriberInterface
{
    public function onRequest($event, $app)
    {
        $access = (array) $event['request']->attributes->get('access');

        foreach ($access as $permission) {
            if (!$app['user']->hasPermission($permission)) {
                throw new HttpException(403, 'Insufficient User Rights.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request' => array('onRequest', -10)
        );
    }
}
