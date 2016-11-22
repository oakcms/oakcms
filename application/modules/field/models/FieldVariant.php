<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\models;

use yii;

class FieldVariant extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%field_variant}}';
    }

    public function rules()
    {
        return [
            [['field_id'], 'required'],
            [['field_id', 'numeric_value'], 'integer'],
            [['value'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_id' => 'Поле',
            'value' => 'Значение',
            'numeric_value' => 'Числовое значение',
        ];
    }

    public static function saveEdit($id, $name, $value)
    {
        $setting = FieldVariant::findOne($id);
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
