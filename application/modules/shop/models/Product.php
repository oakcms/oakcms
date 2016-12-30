<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models;

use app\modules\filter\behaviors\AttachFilterValues;
use app\modules\shop\models\product\ProductQuery;
use app\modules\field\behaviors\AttachFields;
use app\modules\gallery\behaviors\AttachImages;
use yii\helpers\Url;
use yii\helpers\VarDumper;

/**
 * Class Product
 *
 * @property integer $id;
 * @property integer $category_id;
 * @property string $name;
 * @property string $slug;
 * @property string $code;
 * @property string $text;
 *
 * @mixin AttachImages
 * @mixin AttachFields
 * @mixin AttachFilterValues
 */
class Product extends \yii\db\ActiveRecord implements \app\modules\relations\interfaces\Torelate, \app\modules\cart\interfaces\CartElement
{
    const IS_PROMO_YES = 'yes';
    const IS_PROMO_NO = 'no';

    const IS_NEW_YES = 'yes';
    const IS_NEW_NO = 'no';

    const IS_POPULAR_YES = 'yes';
    const IS_POPULAR_NO = 'no';

    const AVAILABLE_YES = 'yes';
    const AVAILABLE_NO = 'no';

    public static function tableName()
    {
        return '{{%shop_product}}';
    }

    function behaviors()
    {
        return [
            'images'     => [
                'class' => AttachImages::className(),
                'mode'  => 'gallery',
            ],
            'slug'       => [
                'class' => 'Zelenin\yii\behaviors\Slug',
            ],
            'relations'  => [
                'class'        => 'app\modules\relations\behaviors\AttachRelations',
                'relatedModel' => 'app\modules\shop\models\Product',
                'inAttribute'  => 'related_ids',
            ],
            'toCategory' => [
                'class'     => \voskobovich\behaviors\ManyToManyBehavior::className(),
                'relations' => [
                    'category_ids' => 'categories',
                ],
            ],
            /*
            'seo' => [
                'class' => 'app\modules\seo\behaviors\SeoFields',
            ],*/
            'filter'     => [
                'class' => AttachFilterValues::className(),
            ],
            'field'      => [
                'class' => AttachFields::className(),
            ],
        ];
    }

    public static function find()
    {
        $return = new ProductQuery(get_called_class());
        $return = $return->with('category');

        return $return;
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['category_id', 'producer_id', 'sort'], 'integer'],
            [['text', 'available', 'is_promo', 'is_popular', 'is_new', 'code'], 'string'],
            [['category_ids'], 'each', 'rule' => ['integer']],
            [['name'], 'string', 'max' => 200],
            [['short_text', 'slug'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'code'            => 'Код (актикул)',
            'category_id'     => 'Главная категория',
            'producer_id'     => 'Бренд',
            'name'            => 'Название',
            'amount'          => 'Остаток',
            'text'            => 'Текст',
            'short_text'      => 'Короткий текст',
            'images'          => 'Картинки',
            'available'       => 'В наличии',
            'sort'            => 'Сортировка',
            'slug'            => 'СЕО-имя',
            'amount_in_stock' => 'Количество на складах',
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function minusAmount($count, $moderator = "false")
    {
        $this->amount = $this->amount - $count;
        $this->save(false);

        return $this;
    }

    public function plusAmount($count, $moderator = "false")
    {
        $this->amount = $this->amount + $count;
        $this->save(false);

        return $this;
    }

    public function setPrice($price, $type = 1)
    {
        if ($priceModel = $this->getPriceModel()) {
            $priceModel->price = $price;

            return $priceModel->save(false);
        } else {
            if($typeModel = PriceType::findOne($type)) {
                $priceModel = new Price;
                $priceModel->product_id = $this->id;
                $priceModel->price = $price;
                $priceModel->type_id = $type;
                $priceModel->name = $typeModel->name;

                return $priceModel->save();
            }
        }

        return false;
    }

    public function getPriceModel($type = 'lower')
    {
        $price = $this->hasOne(Price::className(), ['product_id' => 'id'])->andWhere(['not', ['price' => null]]);

        if ($type == 'lower') {
            $price = $price->orderBy('price ASC')->one();
        } elseif ($type) {
            $price = $price->where(['type_id' => $type])->one();
        } elseif ($defaultType = \Yii::$app->getModule('shop')->getPriceTypeId($this)) {
            $price = $price->where(['type_id' => $defaultType])->one();
        } else {
            $price = $price->orderBy('price DESC')->one();
        }

        return $price;
    }

    public function getPrices()
    {
        $return = $this->hasMany(Price::className(), ['product_id' => 'id'])->orderBy('price ASC');

        return $return;
    }

    public function getPrice($type = 'lower')
    {
        if ($price = $this->getPriceModel($type)) {
            return $price->price;
        }

        return null;
    }

    public function getProduct()
    {
        return $this;
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
        return $this->getPrice();
    }

    public function getPriceByOption($options) {
        if(is_array($options)) {
            $options = serialize($options);
        }
        $modification = $this->getModifications()->andWhere(['filter_values' => $options])->one();
        return $modification->price;
    }

    public function getCartOptions()
    {
        $options = [];

        foreach ($this->modifications as $modification) {
            $modification = unserialize($modification->filter_values);
            foreach ($modification as $filter_id => $filter_variant_id) {

                if ($filters = $this->getFilters()) {
                    foreach ($filters as $filter) {
                        if (($variants = $filter->variants) && $filter->id == $filter_id) {
                            $options[$filter->id]['name'] = $filter->name;
                            foreach ($variants as $variant) {
                                if($variant->id == $filter_variant_id) {
                                    $options[$filter->id]['variants'][$variant->id] = $variant->value;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $options;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSellModel()
    {
        return $this;
    }

    public function getModifications()
    {
        $return = $this->hasMany(Modification::className(), ['product_id' => 'id'])->orderBy('sort DESC, id DESC');

        return $return;
    }

    public function getAmount()
    {
        if ($amount = StockToProduct::find()->where(['product_id' => $this->id])->sum('amount')) {
            return StockToProduct::find()->where(['product_id' => $this->id])->sum('amount');
        } else {
            return 0;
        }
    }

    public function getActionProducts()
    {
        return self::find()->where(['is_promo' => self::IS_PROMO_YES])->available()->all();
    }

    public function getLink()
    {
        return Url::toRoute([\Yii::$app->getModule('shop')->productUrlPrefix, 'slug' => $this->slug]);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])
            ->viaTable('{{%shop_product_to_category}}', ['product_id' => 'id']);
    }

    public function getProducer()
    {
        return $this->hasOne(Producer::className(), ['id' => 'producer_id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        Price::deleteAll(["product_id" => $this->id]);
        ProductToCategory::deleteAll(["product_id" => $this->id]);

        return false;
    }

    public function plusAmountInStock($stock, $count)
    {
        if ($profuctInStock = StockToProduct::find()->where(['product_id' => $this->id, 'stock_id' => $stock])->one()) {
            $profuctInStock->amount = $profuctInStock->amount + $count;

        } else {
            $profuctInStock = new StockToProduct();
            $profuctInStock->amount = $count;
            $profuctInStock->stock_id = $stock;
            $profuctInStock->product_id = $this->id;

        }

        return $profuctInStock;
    }

    public function minusAmountInStock($stock, $count)
    {
        if ($profuctInStock = StockToProduct::find()->where(['product_id' => $this->id, 'stock_id' => $stock])->one()) {
            if ($profuctInStock->amount >= $count) {
                $profuctInStock->amount = $profuctInStock->amount - $count;

            } else {
                return 'На складе всего ' . $profuctInStock->amount . ' единиц товара. Пытались снять ' . $count;
            }
        } else {
            return 'На складе нету такого товара. Пытались снять ' . $count;
        }

        return $profuctInStock->save();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!empty($this->category_id) && !empty($this->id)) {
            if (!(new \yii\db\Query())
                ->select('*')
                ->from('{{%shop_product_to_category}}')
                ->where('product_id =' . $this->id . ' AND category_id = ' . $this->category_id)
                ->all()
            ) {
                \Yii::$app->db->createCommand()->insert('{{%shop_product_to_category}}', [
                    'product_id'  => $this->id,
                    'category_id' => $this->category_id,
                ])->execute();
            }
        }

        return true;
    }
}
