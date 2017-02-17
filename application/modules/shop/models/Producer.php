<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models;

use dosamigos\transliterator\TransliteratorHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use Yii;

class Producer extends \yii\db\ActiveRecord
{
    function behaviors() {
        return [
            'images' => [
                'class' => 'app\modules\gallery\behaviors\AttachImages',
                'mode' => 'single',
            ],
            'field' => [
                'class' => 'app\modules\field\behaviors\AttachFields',
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_producer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['image', 'text'], 'string'],
            [['name', 'slug'], 'string', 'max' => 255],
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название производителя',
            'text' => 'Текст',
            'image' => 'Картинка',
            'slug' => 'SEO Имя',
        ];
    }

     public function getLink() {
        return Url::toRoute(['/producer/view/', 'slug' => $this->slug]);
    }


    public function getByProducts($productFind)
    {
        $return = new Producer;
        $productFind = $productFind->select('producer_id');
        return $return::find()->where(['id' => $productFind]);
    }
}
