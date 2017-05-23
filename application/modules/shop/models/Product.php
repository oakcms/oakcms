<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\shop\models;

use Yii;
use app\modules\filter\behaviors\AttachFilterValues;
use app\modules\shop\models\product\ProductQuery;
use app\modules\field\behaviors\AttachFields;
use app\modules\gallery\behaviors\AttachImages;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;


/**
 * Class Product
 *
 * @property integer      $id
 * @property integer      $category_id
 * @property string       $name
 * @property string       $slug
 * @property string       $code
 * @property string       $short_text
 * @property string       $text
 * @property string       $related_ids
 *
 * @property Modification $modifications
 *
 * @mixin AttachImages
 * @mixin AttachFields
 * @mixin AttachFilterValues
 */
class Product extends \yii\db\ActiveRecord implements
    \app\modules\relations\interfaces\Torelate,
    \app\modules\cart\interfaces\CartElement {
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

    public function behaviors()
    {
        return [
            [
                'class'        => SluggableBehavior::className(),
                'attribute'    => 'name',
                'immutable'    => true,
                'ensureUnique' => true,
            ],
            'images'     => [
                'class' => AttachImages::className(),
                'mode'  => 'gallery',
            ],
            'relations'  => [
                'class'        => \app\modules\relations\behaviors\AttachRelations::className(),
                'relatedModel' => 'app\modules\shop\models\Product',
                'inAttribute'  => 'related_ids',
            ],
            'toCategory' => [
                'class'     => \voskobovich\behaviors\ManyToManyBehavior::className(),
                'relations' => [
                    'category_ids' => 'categories',
                ],
            ],
            'filter'     => [
                'class' => AttachFilterValues::className(),
            ],
            'field'      => [
                'class' => AttachFields::className(),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['category_id', 'name'], 'required'],
            [['category_id', 'producer_id', 'sort'], 'integer'],
            [['text', 'available', 'is_promo', 'is_popular', 'is_new', 'code'], 'string'],
            [['category_ids'], 'each', 'rule' => ['integer']],
            [['name'], 'string', 'max' => 200],
            [['short_text', 'slug'], 'string', 'max' => 255],
            [['slug'], 'filter', 'filter' => 'trim'],
            ['slug', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('shop', 'ID'),
            'code'            => Yii::t('shop', 'Code'),               // Код (актикул)
            'category_id'     => Yii::t('shop', 'Category'),           // Главная категория
            'producer_id'     => Yii::t('shop', 'Producer'),           // Бренд
            'name'            => Yii::t('shop', 'Name'),               // Название
            'amount'          => Yii::t('shop', 'Amount'),             // Остаток
            'text'            => Yii::t('shop', 'Text'),               // Текст
            'short_text'      => Yii::t('shop', 'Short Text'),         // Короткий текст
            'images'          => Yii::t('shop', 'Images'),             // Картинки
            'available'       => Yii::t('shop', 'Available'),          // В наличии
            'sort'            => Yii::t('shop', 'Sort'),               // Сортировка
            'slug'            => Yii::t('shop', 'Slug'),               // СЕО-имя
            'amount_in_stock' => Yii::t('shop', 'Amount in stock')     // Количество на складах
        ];
    }

    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    public function getId()
    {
        return $this->id;
    }

    public function minusAmount($count)
    {
        $this->amount = $this->amount - $count;
        $this->save(false);

        return $this;
    }

    public function plusAmount($count)
    {
        $this->amount = $this->amount + $count;
        $this->save(false);

        return $this;
    }

    public function setPrice($price, $modificationID)
    {
        if ($priceModel = $this->getPriceModel($modificationID)) {
            $priceModel->price = $price;

            return $priceModel->save(false);
        }

        return false;
    }

    public function getPriceModel($modificationID, $type = null)
    {
        $model = Modification::find()->where(['id' => $modificationID])->one();

        if ($type !== null && $model !== null) {
            return $model->getPrice($type, true);
        }

        return $model;
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

    public function getPrice()
    {
        if(($m = Modification::find()->where(['product_id' => $this->id])->orderBy(['id' => SORT_ASC])->one()) !== null) {

            if(($price = Price::find()->where(['modification_id' => $m->id, 'type_id' => 2])->one()) !== null) {
                return $price->price;
            }

            return $m->price;
        }

        return null;
    }

    /**
     * @param $options array|string
     *
     * @return float|null
     */
    public function getPriceByOption($options)
    {
        if (is_array($options)) {
            $options = serialize($options);
        }

        if(
            ($modification = Modification::find()->where([
                'filter_values' => $options,
                'product_id' => $this->id
            ])->one()) !== null
        ) {
            return $modification->price;
        }
        return null;
    }

    public function getModifications()
    {
        return $this->hasMany(Modification::className(), ['product_id' => 'id'])->orderBy('sort ASC');
    }

    public function getCartOptions()
    {
        $options = [];

        foreach ($this->modifications as $modification) {
            if($modification = unserialize($modification->filter_values)) {
                foreach ($modification as $filter_id => $filter_variant_id) {

                    if ($filters = $this->getFilters()) {
                        foreach ($filters as $filter) {
                            if (($variants = $filter->variants) && $filter->id == $filter_id) {
                                $options[$filter->id]['name'] = $filter->name;
                                foreach ($variants as $variant) {
                                    if ($variant->id == $filter_variant_id) {
                                        $options[$filter->id]['variants'][$variant->id] = $variant->value;
                                    }
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

    public function getModification($id = null)
    {
        if ($id !== null) {
            $return = Modification::find()->where(['id' => $id, 'product_id' => $this->id])->orderBy('sort ASC')->all();
        } else {
            $return = Modification::find()->where(['product_id' => $this->id])->orderBy('sort ASC')->one();
        }

        return $return;
    }


    /**
     * @param $options array|string
     * @param $json boolean
     *
     * @return array|null|Modification
     */
    public function getModificationByOptions($options, $json = false) {

        if($json === true) {
            $options = Json::decode($options);
        }

        if(is_array($options)) {
            $opt = [];
            foreach ($options as $k => $option) {
                $opt[(int)$k] = (int)$option;
            }
            $opt = serialize($opt);

            if(
                ($modification = Modification::find()->where([
                    'filter_values' => $opt,
                    'product_id' => $this->id
                ])->orderBy('sort ASC')->one()) === null
            ) {
                $modification = ArrayHelper::getValue($this->modifications, '0');
            }

            return $modification;
        }

        return null;
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

        ProductToCategory::deleteAll(["product_id" => $this->id]);

        foreach ($this->getModifications()->all() as $modification) {
            $modification->delete();
        }

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

    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/shop/product/view', 'slug' => $this->slug];
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return ['/shop/product/view', 'slug' => $model['slug']];
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

    public function getModificationsImages()
    {
        $images = [];
        foreach ($this->getModifications()->all() as $modification) {
            $images[] = $modification->getImage();
        }

        return $images;
    }
}
