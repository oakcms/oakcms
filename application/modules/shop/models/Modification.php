<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\shop\models;

use app\modules\gallery\behaviors\AttachImages;
use dosamigos\transliterator\TransliteratorHelper;
use Yii;
use yii\helpers\Inflector;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;


/**
 * Class Modification
 * @package app\modules\shop\models
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $product_id
 * @property integer $sort
 * @property integer $amount
 * @property float $price
 * @property string $available
 * @property string $code
 * @property string $create_time
 * @property string $update_time
 * @property string $filter_values
 *
 * @mixin AttachImages
 */

class Modification extends \yii\db\ActiveRecord implements \app\modules\cart\interfaces\CartElement
{
    const STATUS_AVAILABLE_YES = 'yes';
    const STATUS_AVAILABLE_NO = 'no';

    function behaviors()
    {
        return [
            'images' => [
                'class' => AttachImages::className(),
                'mode' => 'single',
            ],
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public static function tableName()
    {
        return '{{%shop_product_modification}}';
    }

    public function rules()
    {
        return [
            [['price', 'product_id'], 'required'],
            [['sort', 'amount', 'product_id'], 'integer'],
            [['price'], 'number'],
            [['name', 'available', 'code', 'create_time', 'update_time', 'filter_values'], 'string'],
            [['name'], 'string', 'max' => 55],
            [['slug'], 'filter', 'filter' => 'trim'],
            [['slug'], 'filter', 'filter' => function ($value) {
                if (empty($value)) {
                    return Inflector::slug(TransliteratorHelper::process($this->name));
                } else {
                    return Inflector::slug($value);
                }
            }],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @param null $id
     *
     * @return array|string
     */
    public static function getAvailableVariants($id = null) {
        $availables = [
            self::STATUS_AVAILABLE_YES  => Yii::t('admin', 'Yes'),
            self::STATUS_AVAILABLE_NO   => Yii::t('admin', 'No'),
        ];

        if($id && !empty($availables[$id])) {
            return $availables[$id];
        }

        return $availables;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Товар',
            'name' => 'Название',
            'code' => 'Код (актикул)',
            'images' => 'Картинки',
            'available' => 'В наличии',
            'sort' => 'Сортировка',
            'slug' => 'СЕО-имя',
            'amount' => 'Остаток',
            'create_time' => 'Дата создания',
            'update_time' => 'Дата обновления',
            'filter_values' => 'Сочетание значений фильтров',
        ];
    }

    public function getFiltervariants()
    {
        $return = [];

        if($selected = unserialize($this->filter_values)) {
            foreach($selected as $filter => $value) {
                if($value != '') $return[] = $value;
            }
        }

        return $return;
    }

    public function getId()
    {
        return $this->id;
    }

    public function minusAmount($count)
    {
        $this->amount = $this->amount-$count;

        return $this->save(false);
    }

    public function plusAmount($count)
    {
        $this->amount = $this->amount+$count;

        return $this->save(false);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getCartId()
    {
        return $this->id;
    }

    public function getCartName()
    {
        return $this->name;
    }

    public function getCartPrice()
    {
        return 1;//$this->price;
    }

    public function getCartOptions()
    {
        return '';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSellModel()
    {
        return $this;
    }

    public function getPrices()
    {
        $return = $this->hasMany(Price::className(), ['modification_id' => 'id'])->orderBy('price ASC');

        return $return;
    }

    public function getPriceAction()
    {
        $return = $this->hasMany(Price::className(), ['modification_id' => 'id'])->orderBy('price ASC');

        return $return;
    }

    public function getPrice($type = 'lower', $model = false)
    {
        $price = $this->hasOne(Price::className(), ['modification_id' => 'id']);

        if($type == 'lower') {
            $price = $price->orderBy('price ASC')->one();
        } elseif($type) {
            $price = $price->where(['type_id' => $type])->one();
        } else {
            $price = $price->orderBy('price DESC')->one();
        }

        if($model && $price) {
            return $price;
        }

        if($price) {
            return $price->price;
        }
        return null;
    }

    public function beforeValidate()
    {
//        if($filterValue = yii::$app->request->post('filterValue')) {
//            $filter_values = [];
//            foreach($filterValue as $filterId => $variantId) {
//                $filter_values[$filterId] = $variantId;
//            }
//            $this->filter_values = serialize($filter_values);
//        } else {
//            $this->filter_values = serialize([]);
//        }

        return parent::beforeValidate();
    }

    public static function editField($id, $name, $value)
    {
        $setting = Modification::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }
}
