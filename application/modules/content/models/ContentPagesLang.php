<?php

namespace app\modules\content\models;

use Yii;

/**
 * This is the model class for table "{{%content_pages_lang}}".
 *
 * @property integer $id
 * @property integer $content_pages_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $language
 */
class ContentPagesLang extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_pages_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_pages_id', 'content', 'language'], 'required'],
            [['content_pages_id'], 'integer'],
            [['content'], 'string'],
            [['title', 'slug', 'meta_title', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 10],
        ];
    }

    public function getPage() {
        return $this->hasOne(ContentPages::className(), ['id' => 'content_pages_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'content_pages_id' => Yii::t('content', 'Content Pages ID'),
            'title' => Yii::t('content', 'Title'),
            'slug' => Yii::t('content', 'Slug'),
            'content' => Yii::t('content', 'Content'),
            'meta_title' => Yii::t('content', 'Meta Title'),
            'meta_keywords' => Yii::t('content', 'Meta Keywords'),
            'meta_description' => Yii::t('content', 'Meta Description'),
            'language' => Yii::t('content', 'Language'),
        ];
    }
}
