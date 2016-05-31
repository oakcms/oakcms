<?php

namespace app\modules\system\models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property string $language_id
 * @property string $language
 * @property string $country
 * @property string $name
 * @property string $name_ascii
 * @property integer $status
 */
class Language extends \yii\db\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii', 'status'], 'required'],
            [['status'], 'integer'],
            [['language_id'], 'string', 'max' => 5],
            [['language', 'country'], 'string', 'max' => 3],
            [['name', 'name_ascii'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'language_id' => Yii::t('system', 'Language ID'),
            'language' => Yii::t('system', 'Language'),
            'country' => Yii::t('system', 'Country'),
            'name' => Yii::t('system', 'Name'),
            'name_ascii' => Yii::t('system', 'Name Ascii'),
            'status' => Yii::t('system', 'Status'),
        ];
    }

    public static function getLanguages() {
        return self::find()->where(['status' => self::STATUS_PUBLISHED])->all();
    }
}
