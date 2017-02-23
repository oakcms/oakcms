<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\menu\behaviors;


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
     * Роут на который ссылается активный пункт меню, см. \app\modules\menu\behaviors\components\MenuUrlRule::parseRequest
     * @var string
     */
    public $menuRoute;
    /**
     * Параметры меню, извлекаются из ссылки на которую ссылается пункт меню, см. \app\modules\menu\behaviors\components\MenuUrlRule::parseRequest и \app\modules\menu\models\MenuItem::parseUrl
     * @var array
     */
    public $menuParams;
    /**
     * Роут запроса (в контексте \app\modules\menu\behaviors\components\MenuUrlRule::createUrl) либо
     * необработаный роут запроса (в контексте \app\modules\menu\behaviors\components\MenuUrlRule::parseRequest)
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
