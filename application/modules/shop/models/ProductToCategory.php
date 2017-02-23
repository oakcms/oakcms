<?php

namespace app\modules\shop\models;

use Yii;

/**
 * This is the model class for table "{{%shop_product_to_category}}".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $category_id
 */
class ProductToCategory extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_product_to_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'category_id'], 'required'],
            [['product_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'product_id' => Yii::t('shop', 'Product ID'),
            'category_id' => Yii::t('shop', 'Category ID'),
        ];
    }
}
