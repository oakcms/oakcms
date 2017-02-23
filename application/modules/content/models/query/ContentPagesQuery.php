<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 27.06.2016
 * Project: oakcms
 * File name: ContentCategoryQuery.php
 */

namespace app\modules\content\models\query;


use app\modules\content\models\ContentPages;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\Query;

class ContentPagesQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

    public function published()
    {
        $badcatsQuery = new Query([
            'select' => ['badcats.id'],
            'from' => ['{{%content_pages}} AS unpublished'],
            'join' => [
                ['LEFT JOIN', '{{%content_pages}} AS badcats', 'unpublished.lft <= badcats.lft AND unpublished.rgt >= badcats.rgt']
            ],
            'where' => 'unpublished.status = ' . ContentPages::STATUS_DRAFT,
            'groupBy' => ['badcats.id']
        ]);

        return $this->andWhere(['NOT IN', '{{%content_pages}}.id', $badcatsQuery]);
    }

    /**
     * @return static
     */
    public function excludeRoots()
    {
        return $this->andWhere('{{%content_pages}}.level!=0');
    }
}
