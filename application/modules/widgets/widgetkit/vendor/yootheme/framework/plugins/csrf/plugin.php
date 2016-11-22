<?php

use \YOOtheme\Framework\Csrf\BadTokenException;

return array(

    'name' => 'framework/csrf',

    'main' => function($app) {

        $app->on('init', function($event, $app) {

            if (isset($app['angular'])) {
                $app['angular']->set('csrf', $app['csrf']->generate());
            }

        });

        $app->on('request', function($event, $app) {

            $request = $app['request'];

            if ($request->getMethod() == 'POST' && !$app['csrf']->validate($request->headers->get('X-XSRF-TOKEN'))) {
                throw new BadTokenException(401, 'Invalid CSRF token.');
            }

        });

    }

);
