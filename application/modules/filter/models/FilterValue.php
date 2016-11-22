<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\models;

use yii;

class FilterValue extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%filter_value}}';
    }

    public function rules()
    {
        return [
            [['filter_id', 'item_id', 'variant_id'], 'required'],
            [['filter_id', 'item_id', 'variant_id'], 'integer'],
        ];
    }

    public function getVariant()
    {
        return $this->hasOne(FilterVariant::className(), ['id' => 'variant_id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_id' => 'Фильтр',
            'item_id' => 'Элемент',
            'variant_id' => 'Вариант',
        ];
    }
}
