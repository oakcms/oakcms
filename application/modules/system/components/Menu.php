<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 18.09.2016
 * Project: osnovasite
 * File name: menu.php
 */

namespace app\modules\system\components;


use yii\helpers\Url;

class Menu extends \yii\widgets\Menu
{
    protected function isItemActive($item)
    {

        if($item['url'] == Url::to('')){
            return true;
        } elseif (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        } else {
            return parent::isItemActive($item);
        }
    }
}
