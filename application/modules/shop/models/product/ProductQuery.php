<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models\product;

use app\modules\shop\models\Category;
use app\modules\shop\models\Product;
use yii\db\ActiveQuery;

class ProductQuery extends ActiveQuery
{
    function behaviors()
    {
       return [
           'filter' => [
               'class' => 'app\modules\filter\behaviors\Filtered',
           ],
       ];
    }

    public function available()
    {
         return $this->andWhere(['available' => Product::AVAILABLE_YES]);
    }

    public function category($childCategoriesIds)
    {
         return $this->andwhere(['category_id' => $childCategoriesIds]);
    }
}
