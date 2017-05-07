<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\menu\models;

use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\Query;

/**
 * Class MenuItemQuery
 */
class MenuItemQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

    /**
     * @param $typeId
     * @return static
     */
    public function type($typeId)
    {
        return $this->andWhere(['{{%menu_item}}.menu_type_id' => $typeId]);
    }
    /**
     * @return static
     */
    public function published()
    {
        $badcatsQuery = new Query([
            'select' => ['badcats.id'],
            'from' => ['{{%menu_item}} AS unpublished'],
            'join' => [
                ['LEFT JOIN', '{{%menu_item}} AS badcats', 'unpublished.lft <= badcats.lft AND unpublished.rgt >= badcats.rgt']
            ],
            'where' => 'unpublished.status = ' . MenuItem::STATUS_UNPUBLISHED,
            'groupBy' => ['badcats.id']
        ]);

        return $this->andWhere(['NOT IN', '{{%menu_item}}.id', $badcatsQuery]);
    }

    /**
     * @param $language
     * @return static
     */
    public function language($language)
    {
        return $this->andFilterWhere(['{{%menu_item}}.language' => $language]);
    }

    /**
     * @return static
     */
    public function excludeRoots()
    {
        return $this->andWhere('{{%menu_item}}.lft!=1');
    }

    /**
     * Исключает из выборки пункт меню $item и все его подпункты
     * @param MenuItem $item
     * @return static
     */
    public function excludeItem($item)
    {
        return $this->andWhere('{{%menu_item}}.lft < :excludeLft OR {{%menu_item}}.lft > :excludeRgt', [':excludeLft' => $item->lft, ':excludeRgt' => $item->rgt]);
    }
}
