<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models\category;

use yii\db\ActiveQuery;

class CategoryQuery extends ActiveQuery
{
    public function popular()
    {
         return $this->andWhere(['is_popular' => 'yes']);
    }
}
