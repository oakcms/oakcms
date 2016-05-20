<?php

namespace app\modules\admin\models;

use Yii;
use app\modules\system\helpers\Data;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "{{%modules_modules}}".
 *
 * @property integer $module_id
 * @property string $isFrontend
 * @property string $name
 * @property string $class
 * @property integer $isAdmin
 * @property string $title
 * @property string $icon
 * @property string $settings
 * @property integer $order
 * @property integer $status
 */
class ModulesModules extends \yii\db\ActiveRecord
{

    const CACHE_KEY             = 'modules_cache';
    const STATUS_PUBLISHED      = 1;
    const STATUS_DRAFT          = 0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%modules_modules}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'class', 'isAdmin', 'isFrontend', 'title'], 'required'],
            [['isAdmin', 'isFrontend', 'order', 'status'], 'integer'],
            [['settings'], 'string'],
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
            'module_id' => Yii::t('seoitems', 'ID'),
            'name' => Yii::t('seoitems', 'Name'),
            'class' => Yii::t('seoitems', 'Class'),
            'isAdmin' => Yii::t('seoitems', 'Is Admin'),
            'AdminClass' => Yii::t('seoitems', 'Admin Class'),
            'title' => Yii::t('seoitems', 'Title'),
            'icon' => Yii::t('seoitems', 'Icon'),
            'settings' => Yii::t('seoitems', 'Settings'),
            'order' => Yii::t('seoitems', 'Order'),
            'status' => Yii::t('seoitems', 'Status'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\ModulesModulesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\ModulesModulesQuery(get_called_class());
    }


    public function afterFind()
    {
        parent::afterFind();
        $this->settings = $this->settings !== '' ? json_decode($this->settings, true) : [];
    }

    public static function findAllActive()
    {
        return Data::cache(self::CACHE_KEY, 3600, function() {
            $result = [];
            try {
                foreach (self::find()->where(['status' => self::STATUS_PUBLISHED, 'isFrontend' => self::STATUS_PUBLISHED])->all() as $module) {
                    $module->trigger(self::EVENT_AFTER_FIND);
                    $result[$module->name] = (object)$module->attributes;
                }
            }catch(\yii\db\Exception $e){}

            return $result;
        });
    }
    public static function findAllActiveAdmin()
    {
        //return Data::cache(self::CACHE_KEY, 3600, function() {
            $result = [];
            try {
                foreach (
                    self::find()->where([
                        'status' => self::STATUS_PUBLISHED,
                        'isAdmin' => self::STATUS_PUBLISHED
                    ])->all() as $module) {
                    $module->trigger(self::EVENT_AFTER_FIND);
                    $result[$module->name] = (object)$module->attributes;
                }
            } catch(\yii\db\Exception $e) {}

            return $result;
        //});
    }


    public function setSettings($settings)
    {
        $newSettings = [];
        foreach($this->settings as $key => $value) {
            $newSettings[$key] = is_bool($value) ? ($settings[$key] ? true : false) : ($settings[$key] ? $settings[$key] : '');
        }
        $this->settings = $newSettings;
    }

    public function checkExists($attribute)
    {
        if(!class_exists($this->$attribute)){
            $this->addError($attribute, Yii::t('app', 'Class does not exist'));
        }
    }
}
