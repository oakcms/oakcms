<?php

namespace app\modules\language\models;

use Yii;

/**
 * This is the model class for table "{{%language_translate}}".
 *
 * @property integer $id
 * @property string $language
 * @property string $translation
 * @property string $category
 * @property string $sourceMessage
 * @property object $sourceMessageModel
 */
class LanguageTranslate extends \app\components\ActiveRecord
{

    public $category;
    public $sourceMessage;

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
            'id' => Yii::t('language', 'ID'),
            'language' => Yii::t('language', 'Language'),
            'translation' => Yii::t('language', 'Translation'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceMessageModel()
    {
        return $this->hasOne(LanguageSource::className(), ['id' => 'id']);
    }

    public function afterFind()
    {
        $this->sourceMessage = $this->sourceMessageModel ? $this->sourceMessageModel->message : null;
        $this->category = $this->sourceMessageModel ? $this->sourceMessageModel->category : null;
        return parent::afterFind();
    }
}
