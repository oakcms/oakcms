<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models;

use yii;

/**
 * Class Price
 * @package app\modules\shop\models
 * @property $id
 * @property $name
 * @property $modification_id
 * @property $price
 * @property $type_id
 * @property $amount
 * @property $sort
 */
class Price extends \yii\db\ActiveRecord implements \app\modules\cart\interfaces\CartElement
{

    public static function tableName()
    {
        return '{{%shop_price}}';
    }

    public function rules()
    {
        return [
            [['name', 'modification_id'], 'required'],
            [['name', 'available', 'code'], 'string', 'max' => 100],
            [['price'], 'number'],
            [['modification_id', 'amount', 'type_id'], 'integer'],
            [['modification_id', 'type_id'], 'unique', 'targetAttribute' => ['modification_id', 'type_id']]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'modification_id' => 'Продукт',
            'price' => 'Цена',
            'price_action' => 'Цена акция',
            'code' => 'Артикул',
            'available' => 'Наличие',
            'amount' => 'Остаток',
            'type_id' => 'Тип цены',
            'sort' => 'Приоритет',
        ];
    }

    public function minusAmount($count)
    {
        $this->amount = $this->modification->amount-$count;

        return $this->save(false);
    }

    public function plusAmount($count)
    {
        $this->amount = $this->modification->amount + $count;

        return $this->save(false);
    }

    public function getCartId() {
        return $this->id;
    }

    public function getCartName() {
        return $this->modification->name;
    }

    public function getCartPrice() {
        return $this->price;
    }

    public function getCartOptions()
    {
        return '';
    }

    public function getModification()
    {
        return $this->hasOne(Modification::className(), ['id' => 'modification_id']);
    }

    public function getType()
    {
        return $this->hasOne(PriceType::className(), ['id' => 'type_id']);
    }

    public static function editField($id, $name, $value)
    {
        $setting = self::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }
}
