<?php

namespace app\modules\text\widgets;
use app\modules\text\api\Text;

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
        if(isset($event->output)) {
            $event->output = (new \app\components\ShortCode)->parse('block', $event->output, function($attrs) {
                return is_array($attrs) ? self::getText($attrs) : '';
            });
        }
        return true;
    }

    /**
     * @param array $attrs
     * @return string Html for text block
     */
    public static function getText($attrs) {
        if (isset($attrs['id'])) {
            return Text::get($attrs['id'], true);
        } elseif(isset($attrs['position'])) {
            return Text::get($attrs['position']);
        } else {
            return 'error';
        }
    }
}
