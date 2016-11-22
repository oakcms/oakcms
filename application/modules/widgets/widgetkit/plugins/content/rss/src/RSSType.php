<?php

namespace YOOtheme\Widgetkit\Content\rss;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\Routing\ControllerInterface;
use YOOtheme\Widgetkit\Content\Type;


class RSSType extends Type implements ControllerInterface
{
    /**
     * @param Application $app
     */
    public function main(Application $app)
    {
        parent::main($app);

        $app->on('init', function ($event, $app) {

            $app['rss'] = function () use ($app) {

                return new RSSApp($app);
            };

        }, -5);

        $this['controllers']->add($this);
    }

    public static function getRoutes()
    {
        return array();
    }
}