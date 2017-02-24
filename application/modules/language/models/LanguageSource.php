<?php

namespace app\modules\language\models;

use Yii;

/**
 * This is the model class for table "{{%language_source}}".
 *
 * @property integer $id
 * @property string $category
 * @property string $message
 */
class LanguageSource extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language_source}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('language', 'ID'),
            'category' => Yii::t('language', 'Category'),
            'message' => Yii::t('language', 'Message'),
        ];
    }
}
