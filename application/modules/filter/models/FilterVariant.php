<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\filter\models;

/**
 * Class FilterVariant
 * @package app\modules\filter\models
 *
 * @property integer $id
 * @property integer $filter_id
 * @property integer $numeric_value
 * @property string  $value
 */
class FilterVariant extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%filter_variant}}';
    }

    public static function saveEdit($id, $name, $value)
    {
        $setting = FilterVariant::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }

    function behaviors()
    {
        return [
            'images' => [
                'class' => 'app\modules\gallery\behaviors\AttachImages',
                'mode'  => 'single',
            ],
        ];
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
            'id'            => 'ID',
            'filter_id'     => 'Фильтр',
            'value'         => 'Значение',
            'numeric_value' => 'Числовое значение',
        ];
    }

    public function getFilter()
    {
        return $this->hasOne(Filter::className(), ['id' => 'filter_id']);
    }

    /**
     * @param $id integer
     * @param $filter_id integer
     *
     * @return null|array
     */
    public static function getVariantValue($id, $filter_id)
    {
        return self::find()
            ->joinWith(['filter'], false)
            ->select([self::tableName() . '.value', Filter::tableName() . '.name'])
            ->where([self::tableName() . '.id' => $id, self::tableName() . '.filter_id' => $filter_id])
            ->asArray()
            ->one();
    }

    public function beforeValidate()
    {
        if (empty($this->numeric_value)) {
            $this->numeric_value = (int)$this->value;
        }

        return true;
    }
}
