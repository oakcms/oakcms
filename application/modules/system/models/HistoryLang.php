<?php

namespace app\modules\system\models;

use Yii;

/**
 * This is the model class for table "{{%history_lang}}".
 *
 * @property integer $id
 * @property integer $history_id
 * @property string $content
 * @property string $language
 */
class HistoryLang extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%history_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['history_id', 'content', 'language'], 'required'],
            [['history_id'], 'integer'],
            [['content'], 'string'],
            [['language'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('system', 'ID'),
            'history_id' => Yii::t('system', 'History ID'),
            'content' => Yii::t('system', 'Content'),
            'language' => Yii::t('system', 'Language'),
        ];
    }
}
