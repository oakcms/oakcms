<?php

namespace app\modules\content\models;

use Yii;

/**
 * This is the model class for table "{{%content_category_lang}}".
 *
 * @property integer $id
 * @property integer $content_category_id
 * @property string $slug
 * @property string $title
 * @property string $content
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $settings
 * @property string $language
 */
class ContentCategoryLang extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_category_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_category_id', 'title', 'name', 'language'], 'required'],
            [['content_category_id'], 'integer'],
            [['content', 'settings'], 'string'],
            [['slug'], 'string', 'max' => 150],
            [['title', 'meta_title', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'content_category_id' => Yii::t('content', 'Content Category ID'),
            'slug' => Yii::t('content', 'Slug'),
            'title' => Yii::t('content', 'Title'),
            'content' => Yii::t('content', 'Content'),
            'meta_title' => Yii::t('content', 'Meta Title'),
            'meta_keywords' => Yii::t('content', 'Meta Keywords'),
            'meta_description' => Yii::t('content', 'Meta Description'),
            'settings' => Yii::t('content', 'Settings'),
            'language' => Yii::t('content', 'Language'),
        ];
    }
}
