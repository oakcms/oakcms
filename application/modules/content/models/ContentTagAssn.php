<?php

namespace app\modules\content\models;

use Yii;

/**
 * This is the model class for table "{{%content_tag_assn}}".
 *
 * @property integer $content_id
 * @property integer $content_tags_id
 */
class ContentTagAssn extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_tag_assn}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_id', 'content_tags_id'], 'required'],
            [['content_id', 'content_tags_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content_id' => Yii::t('content', 'Content ID'),
            'content_tags_id' => Yii::t('content', 'Content Tags ID'),
        ];
    }
}
