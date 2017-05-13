<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\models;

use Yii;

/**
 * Class FilterVariant
 * @package app\modules\filter\models
 *
 * @property integer $id
 * @property integer $filter_id
 * @property string $value
 */
class FilterVariant extends \yii\db\ActiveRecord
{
    function behaviors()
    {
        return [
            'images' => [
                'class' => 'app\modules\gallery\behaviors\AttachImages',
                'mode' => 'single',
            ],
        ];
    }

    public static function tableName()
    {
        return '{{%filter_variant}}';
    }

    public function rules()
    {
        return [
            [['filter_id'], 'required'],
            [['filter_id', 'numeric_value'], 'integer'],
            [['value'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_id' => 'Фильтр',
            'value' => 'Значение',
            'numeric_value' => 'Числовое значение',
        ];
    }

    public function getFilter()
    {
        return $this->hasOne(Filter::className(), ['id' => 'filter_id']);
    }

    public static function saveEdit($id, $name, $value)
    {
        $setting = FilterVariant::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }

    public function beforeValidate()
    {
        if(empty($this->numeric_value)) {
            $this->numeric_value = (int)$this->value;
        }

        return true;
    }
}
