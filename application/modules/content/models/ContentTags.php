<?php

namespace app\modules\content\models;

use Yii;

/**
 * This is the model class for table "{{%content_tags}}".
 *
 * @property integer $id
 * @property integer $frequency
 * @property string $name
 */
class ContentTags extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['frequency', 'name'], 'required'],
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'frequency' => Yii::t('content', 'Frequency'),
            'name' => Yii::t('content', 'Name'),
        ];
    }
}
