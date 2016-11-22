<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models;

use Yii;

class Incoming extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return '{{%shop_incoming}}';
    }

    public function rules()
    {
        return [
            [['content'], 'string'],
            [['date'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Дата',
            'content' => 'Содержание заказа',
        ];
    }
}
