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
            [['link', 'title'], 'required'],
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
            'id' => Yii::t('app', 'ID'),
            'link' => Yii::t('app', 'Link'),
            'title' => Yii::t('app', 'Title'),
            'keywords' => Yii::t('app', 'Keywords'),
            'description' => Yii::t('app', 'Description'),
            'canonical' => Yii::t('app', 'Canonical'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
