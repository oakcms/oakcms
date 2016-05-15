<?php

namespace app\modules\seo\models;

use Yii;

/**
 * This is the model class for table "{{%seo_items}}".
 *
 * @property integer $id
 * @property string $link
 * @property string $title
 * @property string $keywords
 * @property string $description
 * @property string $canonical
 * @property integer $status
 */
class SeoItems extends \yii\db\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%seo_items}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['keywords', 'description'], 'string'],
            [['status'], 'integer'],
            [['link', 'title', 'canonical'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('seoitems', 'ID'),
            'link' => Yii::t('seoitems', 'Link'),
            'title' => Yii::t('seoitems', 'Title'),
            'keywords' => Yii::t('seoitems', 'Keywords'),
            'description' => Yii::t('seoitems', 'Description'),
            'canonical' => Yii::t('seoitems', 'Canonical'),
            'status' => Yii::t('seoitems', 'Status'),
        ];
    }
}
