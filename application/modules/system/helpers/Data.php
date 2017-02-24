<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 20.05.2016
 * Project: oakcms
 * File name: Data.php
 */

namespace app\modules\system\helpers;

use Yii;

class Data
{
    public static function cache($key, $duration, $callable)
    {
        $cache = Yii::$app->cache;
        if($cache->exists($key)){
            $data = $cache->get($key);
        }
        else{
            $data = $callable();

            if($data) {
                $cache->set($key, $data, $duration);
            }
        }
        return $data;
    }

    public static function getLocale()
    {
        return strtolower(substr(Yii::$app->language, 0, 2));
    }
}
