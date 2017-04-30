<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\models;

class FieldValue extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%field_value}}';
    }

    public function rules()
    {
        return [
            [['field_id', 'item_id'], 'required'],
            [['value', 'variant_id'], 'required', 'when' => function ($model) {
                return empty($model->variant_id) && empty($model->value);
            }],
            [['field_id', 'item_id', 'variant_id', 'numeric_value'], 'integer'],
            [['value'], 'string'],
        ];
    }

    public function getVariant()
    {
        return $this->hasOne(FieldVariant::className(), ['id' => 'variant_id']);
    }

    public function getField()
    {
        return $this->hasOne(Field::className(), ['id' => 'field_id']);
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'field_id'      => 'Фильтр',
            'item_id'       => 'Элемент',
            'variant_id'    => 'Вариант',
            'value'         => 'Значение',
            'numeric_value' => 'Числовое значение',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (empty($this->value) && !empty($this->variant_id)) {
            $this->value = $this->variant->value;
            $this->save(false);
        }

        if ($this->numeric_value != (int)$this->value) {
            $this->numeric_value = (int)$this->value;
            $this->save(false);
        }

        return true;
    }
}
