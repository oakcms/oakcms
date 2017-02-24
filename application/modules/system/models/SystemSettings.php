<?php

namespace app\modules\system\models;

use app\modules\language\models\Language;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%system_settings}}".
 *
 * @property integer $id
 * @property string $param_name
 * @property string $param_value
 * @property string $type
 */
class SystemSettings extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['param_name', 'param_value', 'type'], 'required'],
            [['param_name'], 'string', 'max' => 100],
            [['param_value', 'type'], 'string', 'max' => 255],
            [['param_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('seoitems', 'ID'),
            'param_name'        => Yii::t('seoitems', 'Param Name'),
            'param_value'       => Yii::t('seoitems', 'Param Value'),
            'type'              => Yii::t('seoitems', 'Type'),
        ];
    }

    /**
     * @return string
     */
    public function renderField()
    {
        switch ($this->type) {
            case 'textInput':
                return Html::textInput($this->param_name, $this->param_value, ['class' => 'form-control']);
            break;
            case 'textarea':
                return Html::textarea($this->param_name, $this->param_value, ['class' => 'form-control']);
            break;
            case 'checkbox':
                return
                    Html::hiddenInput($this->param_name, 0) .
                    \oakcms\bootstrapswitch\Switcher::widget([
                        'name' => $this->param_name,
                        'checked' => $this->param_value
                    ]);
            break;
            case 'language':
                return Html::dropDownList($this->param_name, $this->param_value, ArrayHelper::map(Language::getLanguages(), 'language_id', 'name'), ['class' => 'form-control']);
            break;
            case 'getTheme':

                $files = scandir(Yii::getAlias('@app').'/templates/frontend');

                $items = [];
                foreach ($files as $file) {
                    //if(!is_file($file) AND $file != '.' AND $file != '..') {
                    if($file != '.' AND $file != '..') {
                        $items[$file] = $file;
                    }
                }

                return Html::dropDownList($this->param_name, $this->param_value, $items, ['class' => 'form-control']);
            break;
        }
    }
}
