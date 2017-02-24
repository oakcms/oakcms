<?php

namespace app\modules\widgets\widgets;

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 08.09.2016
 * Project: osnovasite
 * File name: ShortCode.php
 */
class ShortCode extends \app\components\ShortCode
{
    public static function shortCode($event)
    {
        if(isset($event->output)){
            if (!$app = include(__DIR__.'/../widgetkit/widgetkit_yii2.php')) {
                return;
            }

            $event->output = $app['shortcode']->parse('widgetkit', $event->output, function($attrs) use ($app) {
                return $app->renderWidget($attrs);
            });
        }
        return true;
    }
}
