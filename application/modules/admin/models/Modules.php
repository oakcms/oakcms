<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\admin\models;

use app\components\ActiveRecord;
use app\modules\system\helpers\Data;
use himiklab\sortablegrid\SortableGridBehavior;
use Yii;

/**
 * This is the model class for table "{{%admin_modules}}".
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
class Modules extends ActiveRecord
{

    const CACHE_KEY_FRONTEND = 'modules_cache_frontend';
    const CACHE_KEY_BACKEND = 'modules_cache_backend';
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
//    public static function tableName()
//    {
//        return '{{%admin_modules}}';
//    }

    public function behaviors()
    {
        return [
            'sortable' => [
                'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
                'query' => self::find(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'class', 'isAdmin', 'isFrontend', 'title'], 'required'],
            [['isAdmin', 'isFrontend', 'order', 'status'], 'integer'],
            //[['settings'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['class', 'bootstrapClass', 'title'], 'string', 'max' => 128],
            [['icon'], 'string', 'max' => 32],
            [['name'], 'unique'],
            ['class',  'match', 'pattern' => '/^[\w\\\]+$/'],
            ['class',  'checkExists'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_id'  => Yii::t('admin', 'ID'),
            'name'       => Yii::t('admin', 'Name'),
            'class'      => Yii::t('admin', 'Class'),
            'isAdmin'    => Yii::t('admin', 'Is Admin'),
            'AdminClass' => Yii::t('admin', 'Admin Class'),
            'title'      => Yii::t('admin', 'Title'),
            'icon'       => Yii::t('admin', 'Icon'),
            'settings'   => Yii::t('admin', 'Settings'),
            'order'      => Yii::t('admin', 'Order'),
            'status'     => Yii::t('admin', 'Status'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\ModulesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\ModulesQuery(get_called_class());
    }

    public function getId() {
        return $this->module_id;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->settings = $this->settings !== '' ? json_decode($this->settings, true) : self::getDefaultSettings($this->name);
    }

    public function checkExists($attribute)
    {
        if(!class_exists($this->$attribute)){
            $this->addError($attribute, Yii::t('admin', 'Class does not exist'));
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(!$this->settings || !is_array($this->settings)){
                $this->settings = self::getDefaultSettings($this->name);
            }
            $this->settings = json_encode($this->settings);
            return true;
        } else {
            return false;
        }
    }

    public static function findAllActive()
    {
        return Data::cache(self::CACHE_KEY_FRONTEND, 3600, function () {
            $result = [];
            try {
                foreach (self::find()->where(['status' => self::STATUS_PUBLISHED])->orderBy('order')->all() as $module) {
                    $module->trigger(self::EVENT_AFTER_FIND);
                    $result[$module->name] = (object)$module->attributes;
                }
            } catch (\yii\db\Exception $e) {
            }

            return $result;
        });
    }

    public static function findAllActiveAdmin()
    {
        return Data::cache(self::CACHE_KEY_BACKEND, 3600, function() {
            $result = [];
            try {
                foreach (
                    self::find()->where([
                        'status' => self::STATUS_PUBLISHED,
                        'isAdmin' => self::STATUS_PUBLISHED
                    ])->orderBy('order')->all() as $module) {
                    $module->trigger(self::EVENT_AFTER_FIND);
                    $result[$module->name] = (object)$module->attributes;
                }
            } catch (\yii\db\Exception $e) {}

            return $result;
        });
    }


    public function setSettings($settings)
    {
        $newSettings = [];
        if(is_array($this->settings)) {
            foreach ($this->settings as $key => $value) {
                $newSettings[$key]['value'] = is_bool($value['value']) ? ($settings[$key]  ? true : false) : ((isset($settings[$key])) ? $settings[$key]  : '');
                $newSettings[$key]['type'] = $value['type'];
            }
        }
        $this->settings = $newSettings;
    }

    static function getDefaultSettings($moduleName)
    {
        $modules = Yii::$app->getModule('admin')->activeModules;
        if(isset($modules[$moduleName])){
            return Yii::createObject($modules[$moduleName]->class, [$moduleName])->settings;
        } else {
            return [];
        }
    }
}
