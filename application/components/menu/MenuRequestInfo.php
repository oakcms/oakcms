<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\components\menu;


/**
 * Class MenuRequestInfo
 * Обертка для данных о запросе
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRequestInfo extends \yii\base\Object
{
    /**
     * Карта меню в контексте которой рассматривается текущий запрос (карты различаются в зависимости от языка)
     * @var MenuMap
     */
    public $menuMap;
    /**
     * Роут на который ссылается активный пункт меню, см. \app\components\menu\components\MenuUrlRule::parseRequest
     * @var string
     */
    public $menuRoute;
    /**
     * Параметры меню, извлекаются из ссылки на которую ссылается пункт меню, см. \app\components\menu\components\MenuUrlRule::parseRequest и \app\modules\menu\models\MenuItem::parseUrl
     * @var array
     */
    public $menuParams;
    /**
     * Роут запроса (в контексте \app\components\menu\components\MenuUrlRule::createUrl) либо
     * необработаный роут запроса (в контексте \app\components\menu\components\MenuUrlRule::parseRequest)
     * необработанный роут = роут запроса - путь "подходящего" пункта меню
     * @var string
     */
    public $requestRoute;
    /**
     * Параметры запроса
     * @var array
     */
    public $requestParams;
}
