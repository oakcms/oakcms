<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace app\modules\menu\models;


use dosamigos\transliterator\TransliteratorHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;

/**
 * This is the model class for table "grom_menu_type".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property string $title
 * @property string $alias
 * @property string $note
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $lock
 *
 * @property MenuItem[] $items
 */
class MenuType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'lock'], 'integer'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 1024],
            [['alias'], 'filter', 'filter' => 'trim'],
            [['alias'], 'filter', 'filter' => function($value){
                    if(empty($value)) {
                        return Inflector::slug(TransliteratorHelper::process($this->title));
                    } else {
                        return Inflector::slug($value);
                    }
                }],
            [['alias'], 'unique'],
            [['alias'], 'required', 'enableClientValidation' => false],
            [['alias', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('menu', 'ID'),
            'status' => Yii::t('menu', 'Status'),
            'title' => Yii::t('menu', 'Title'),
            'alias' => Yii::t('menu', 'Alias'),
            'note' => Yii::t('menu', 'Note'),
            'created_at' => Yii::t('menu', 'Created At'),
            'updated_at' => Yii::t('menu', 'Updated At'),
            'created_by' => Yii::t('menu', 'Created By'),
            'updated_by' => Yii::t('menu', 'Updated By'),
            'lock' => Yii::t('menu', 'Lock'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    public function optimisticLock()
    {
        return 'lock';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(MenuItem::className(), ['menu_type_id'=>'id'])->orderBy('lft');
    }
}
