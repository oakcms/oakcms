<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models;

use yii;

class PriceType extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%shop_price_type}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['condition'], 'string'],
            [['sort'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'sort' => 'Сортировка',
            'condition' => 'Условие',
        ];
    }
}
