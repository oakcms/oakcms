<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 *
 * Use the
 *
 * ```php
 * use app\modules\admin\components\behaviors\SettingModel;
 *
 * public function behaviors()
 * {
 *     return [
 *         SettingModel::className(),
 *     ];
 * }
 * ```
 *
 * or
 *
 * ```php
 * use app\modules\admin\components\behaviors\SettingModel;
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *              'class' => SettingModel::className(),
 *              'settingsField' => 'settings'
 *          ]
 *     ];
 * }
 * ```
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 03.07.2016
 * Project: oakcms
 * File name: SettingModel.php
 */

namespace app\modules\admin\components\behaviors;

use app\components\ActiveRecord;
use app\modules\admin\models\ModulesModules;
use app\modules\text\controllers\backend\DefaultController;
use yii\helpers\VarDumper;

class SettingModel extends \yii\base\Behavior
{
    /**
     * @var string the settings field used in the table. Determines the settings to query | save.
     */
    public $settingsField = 'settings';
    public $module = '';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave'
        ];
    }

    /**
     * @param \yii\base\Event $event
     */
    public function afterFind($event)
    {
        $this->settingsAfterLanguage();
    }

    /**
     * @param \yii\base\Event $event
     */
    public function beforeSave($event)
    {
        if(!$this->owner->{$this->settingsField} || !is_array($this->owner->{$this->settingsField})){
            $this->owner->{$this->settingsField} = ModulesModules::getDefaultSettings($this->module);
        }
        $this->owner->{$this->settingsField} = json_encode($this->owner->{$this->settingsField});
    }

    public function settingsAfterLanguage()
    {
        if($this->owner->{$this->settingsField} !== '' AND $this->owner->{$this->settingsField} !== null) {
            $this->owner->{$this->settingsField} = json_decode($this->owner->{$this->settingsField}, true);
        } elseif($this->module === false) {

        } else {
            $this->owner->{$this->settingsField} = ModulesModules::getDefaultSettings($this->module);
        }
    }

    public function setSetting($settings, $layout = null)
    {
        $newSettings = [];
        if($this->module === false && $layout !== null) {
            foreach (DefaultController::getLayouts($layout)[0]['settings'] as $key => $value) {
                $newSettings[$key] = [
                    'value' => is_bool($value['value']) ? ($settings[$key] ? true : false) : ($settings[$key] ? $settings[$key] : ''),
                    'type' => $value['type']
                ];
            }
        } else {
            foreach ($this->owner->{$this->settingsField} as $key => $value) {
                $newSettings[$key] = [
                    'value' => is_bool($value['value']) ? ($settings[$key] ? true : false) : ($settings[$key] ? $settings[$key] : ''),
                    'type' => $value['type']
                ];
            }
        }
        $this->owner->{$this->settingsField} = $newSettings;
    }
}
