<?php

namespace app\modules\widgets\models;

use Yii;

/**
 * This is the model class for table "{{%widgetkit}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $data
 */
class Widgetkit extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%widgetkit}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'data'], 'required'],
            [['data'], 'string'],
            [['name', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('widgets', 'ID'),
            'name' => Yii::t('widgets', 'Name'),
            'type' => Yii::t('widgets', 'Type'),
            'data' => Yii::t('widgets', 'Data'),
        ];
    }
}
