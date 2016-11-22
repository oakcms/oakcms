<?php

namespace YOOtheme\Framework\Routing;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\Event\Event;
use YOOtheme\Framework\Event\EventSubscriberInterface;

class ResponseListener implements EventSubscriberInterface
{
    /**
     * Converts a string to a response.
     *
     * @param Event       $event
     * @param Application $app
     */
    public function stringToResponse($event, $app)
    {
        $response = $event['response'];

        if (!(null === $response || is_array($response) || $response instanceof Response || (is_object($response) && !method_exists($response, '__toString')))) {

            $event['response'] = $app['response']->create((string) $response);
            $event->stopPropagation();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'response' => array('stringToResponse', -10)
        );
    }
}
