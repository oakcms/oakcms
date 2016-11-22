<?php

namespace YOOtheme\Widgetkit\Content\instagram;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\Routing\ControllerInterface;
use YOOtheme\Widgetkit\Content\Type;


class InstagramType extends Type implements ControllerInterface
{
    /**
     * @param Application $app
     */
    public function main(Application $app)
    {
        parent::main($app);

        $app->on('init', function ($event, $app) {

            $app['instagram'] = function () use ($app) {

                return new InstagramApp($app);
            };

        }, -5);

        $this['controllers']->add($this);
    }

    public static function getRoutes()
    {
        return array();
    }
}