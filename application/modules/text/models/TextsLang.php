<?php

namespace app\modules\text\models;

use Yii;

/**
 * This is the model class for table "{{%texts_lang}}".
 *
 * @property integer $id
 * @property integer $texts_id
 * @property string $title
 * @property string $subtitle
 * @property string $text
 * @property string $language
 */
class TextsLang extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%texts_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['texts_id', 'title', 'text', 'language'], 'required'],
            [['texts_id'], 'integer'],
            [['text'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['subtitle'], 'string', 'max' => 500],
            [['language'], 'string', 'max' => 10],
            ['settings', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('menu', 'ID'),
            'texts_id' => Yii::t('menu', 'Texts ID'),
            'title' => Yii::t('menu', 'Title'),
            'subtitle' => Yii::t('menu', 'Subtitle'),
            'text' => Yii::t('menu', 'Text'),
            'language' => Yii::t('menu', 'Language'),
        ];
    }
}
