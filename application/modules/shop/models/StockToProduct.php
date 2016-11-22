<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveQuery;

class StockToProduct extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return '{{%shop_stock_to_product}}';
    }

    public function rules()
    {
        return [
            [['product_id', 'stock_id', 'amount'], 'required'],
            [['product_id', 'stock_id', 'amount'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Адрес',
            'name' => 'Название',
            'text' => 'Текст',
        ];
    }
}
