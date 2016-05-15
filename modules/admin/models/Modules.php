<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "{{%modules}}".
 *
 * @property integer $module_id
 * @property string $name
 * @property string $class
 * @property string $title
 * @property string $icon
 * @property string $settings
 * @property integer $order
 * @property integer $status
 */
class Modules extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%modules}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'class', 'title', 'icon', 'settings'], 'required'],
            [['settings'], 'string'],
            [['order', 'status'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['class', 'title'], 'string', 'max' => 128],
            [['icon'], 'string', 'max' => 32],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_id' => Yii::t('admin', 'Module ID'),
            'name' => Yii::t('admin', 'Name'),
            'class' => Yii::t('admin', 'Class'),
            'title' => Yii::t('admin', 'Title'),
            'icon' => Yii::t('admin', 'Icon'),
            'settings' => Yii::t('admin', 'Settings'),
            'order' => Yii::t('admin', 'Order'),
            'status' => Yii::t('admin', 'Status'),
        ];
    }
}
