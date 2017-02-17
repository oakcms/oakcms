<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\content\models\query;


use app\components\ActiveQuery;

class ContentArticleQuery extends ActiveQuery
{

    public function order($order) {
        switch ($order) {
            case "date":
                $this->orderBy(['created_at' => SORT_ASC]);
                break;
            case "rdate":
                $this->orderBy(['created_at' => SORT_DESC]);
                break;
            case "modified":
                $this->orderBy(['updated_at' => SORT_ASC]);
                break;
            case "rmodified":
                $this->orderBy(['updated_at' => SORT_DESC]);
                break;
            case "alpha":
                $this->orderBy(['{{%content_articles_lang}}.title' => SORT_ASC]);
                break;
            case "ralpha":
                $this->orderBy(['{{%content_articles_lang}}.title' => SORT_DESC]);
                break;
            case "hits":
                $this->orderBy(['hits' => SORT_ASC]);
                break;
            case "rhits":
                $this->orderBy(['hits' => SORT_DESC]);
                break;
            case "random":
                $this->orderBy(new \yii\db\Expression('rand()'));
                break;
        }
    }
}
