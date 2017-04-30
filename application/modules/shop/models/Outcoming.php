<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\shop\models;


/**
 * Class Outcoming
 * @package app\modules\shop\models
 *
 * @var integer $stock_id
 * @var integer $product_id
 * @var integer $user_id
 * @var integer $order_id
 * @var integer $count
 * @var integer $date
 */
class Outcoming extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return '{{%shop_outcoming}}';
    }

    public function rules()
    {
        return [
            [['stock_id', 'product_id'], 'required'],
            [['date', 'stock_id', 'product_id', 'user_id', 'order_id', 'count'], 'integer'],
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
