<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\menu\behaviors;


use app\modules\menu\models\MenuItem;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class MenuManager
 * Компонент меню, доступен через Yii::$app->menuManager
 * Данный компонент инициирует маршрутизацию меню, через MenuUrlRule, а также дает доступ к активному пнукту меню
 * и картам пуктов меню [[MenuManager::getMenuMap]]
 * @package oakcms
 *
 * @property MenuItem $activeMenu
 * @property integer[] $activeMenuIds
 */

class MenuManager extends \yii\base\Object
{
    /**
     * @var MenuItem
     */
    private $_activeMenu;
    private $_activeMenuIds = [];
    private $_maps = [];

    public function init()
    {
        Yii::$app->urlManager->addRules([Yii::createObject([
            'class' => MenuUrlRule::className(),
            'menuManager' => $this
        ])], false); //вставляем в начало списка

    }

    /**
     * @param null $language
     * @return MenuMap
     */
    public function getMenuMap($language = null)
    {
        $language or $language = Yii::$app->language;
        if(!isset($this->_maps[$language]))
            $this->_maps[$language] = Yii::createObject([
                'class' => MenuMap::className(),
                'language' => $language,
            ]);

        return $this->_maps[$language];
    }

    public function setActiveMenu($value)
    {
        $this->_activeMenu = $value;
    }

    public function getActiveMenu()
    {
        return $this->_activeMenu;
    }

    public function addActiveMenuId($value)
    {
        $this->_activeMenuIds[] = $value;
    }

    public function setActiveMenuIds($value)
    {
        $this->_activeMenuIds = $value;
    }

    public function getActiveMenuIds()
    {
        return $this->_activeMenuIds;
    }
}
