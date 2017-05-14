<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\filter\models;

/**
 * Class FieldRelationValue
 * @package app\modules\filter\models
 *
 * @property integer $id
 * @property integer $filter_id
 * @property integer $value
 */
class FieldRelationValue extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%filter_relation_value}}';
    }

    public function rules()
    {
        return [
            [['filter_id'], 'required'],
            [['filter_id', 'value'], 'integer'],
        ];
    }

    public function getFilters()
    {
        return $this->hasOne(Filter::className(), ['id' => 'filter_id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_id' => 'Фильтр',
            'value' => 'Значение',
        ];
    }
}
