<?php

namespace app\modules\language\models;

use Yii;

/**
 * This is the model class for table "{{%language_translate}}".
 *
 * @property integer $id
 * @property string $language
 * @property string $translation
 */
class LanguageTranslate extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language_translate}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language'], 'required'],
            [['id'], 'integer'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'language' => Yii::t('content', 'Language'),
            'translation' => Yii::t('content', 'Translation'),
        ];
    }
}
