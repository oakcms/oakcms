<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\shop\models;

use Yii;


/**
 * Class PriceType
 * @package app\modules\shop\models
 * @property $id
 * @property $name
 * @property $condition
 * @property $sort
 */
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
