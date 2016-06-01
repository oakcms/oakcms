<?php

namespace app\modules\content\models;

use Yii;

/**
 * This is the model class for table "content_articles_lang".
 *
 * @property integer $content_articles_id
 * @property string $slug
 * @property string $title
 * @property string $content
 * @property string $link
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $image
 * @property string $language
 */
class ContentArticlesLang extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content_articles_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_articles_id', 'slug', 'title', 'content', 'language'], 'required'],
            [['content_articles_id'], 'integer'],
            [['content'], 'string'],
            [['slug'], 'string', 'max' => 150],
            [['title', 'link', 'meta_title', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 300],
            [['language'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content_articles_id' => Yii::t('content', 'Content Articles ID'),
            'slug' => Yii::t('content', 'Slug'),
            'title' => Yii::t('content', 'Title'),
            'content' => Yii::t('content', 'Content'),
            'link' => Yii::t('content', 'Link'),
            'meta_title' => Yii::t('content', 'Meta Title'),
            'meta_keywords' => Yii::t('content', 'Meta Keywords'),
            'meta_description' => Yii::t('content', 'Meta Description'),
            'image' => Yii::t('content', 'Image'),
            'language' => Yii::t('content', 'Language'),
        ];
    }
}
