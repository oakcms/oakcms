<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 08.06.2016
 * Project: oakcms
 * File name: Html.php
 */

namespace app\modules\admin\widgets;


use app\modules\menu\models\MenuType;
use app\modules\widgets\models\Widgetkit;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Html extends \yii\bootstrap\Html
{

    /**
     * @param $item
     * @return string
     */
    public static function settingField($key, $item, $traslateCategory)
    {
        $return = '';
        switch ($item['type']) {
            case 'checkbox':
                $return =
                    parent::beginTag('div', ['class' => 'form-group']) .
                    parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                    \Yii::t($traslateCategory, Inflector::camel2words($key)) .
                    parent::endTag('label') .
                    parent::beginTag('div', ['class' => 'col-md-9']) .
                    parent::hiddenInput('Settings['.$key.']', 0) .
                    \oakcms\bootstrapswitch\Switcher::widget([
                        'id' => 'wid'.uniqid(),
                        'name' => 'Settings['.$key.']',
                        'checked' => $item['value']
                    ]) .
                    parent::endTag('div') .
                    parent::endTag('div');
                break;
            case 'textInput':
                $return =
                    parent::beginTag('div', ['class' => 'form-group']) .
                    parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                    \Yii::t($traslateCategory, Inflector::camel2words($key)) .
                    parent::endTag('label') .
                    parent::beginTag('div', ['class' => 'col-md-9']) .
                    parent::textInput('Settings['.$key.']', $item['value'], ['class' => 'form-control']).
                    parent::endTag('div') .
                    parent::endTag('div');
                break;
            case 'textarea':
                $return =
                    parent::beginTag('div', ['class' => 'form-group']) .
                    parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                    \Yii::t($traslateCategory, Inflector::camel2words($key)) .
                    parent::endTag('label') .
                    parent::beginTag('div', ['class' => 'col-md-9']) .
                    parent::textarea('Settings['.$key.']', $item['value'], ['class' => 'form-control']).
                    parent::endTag('div') .
                    parent::endTag('div');
                break;
            case 'mediaInput':
                $return =
                    parent::beginTag('div', ['class' => 'form-group']) .
                    parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                    \Yii::t($traslateCategory, Inflector::camel2words($key)) .
                    parent::endTag('label') .
                    parent::beginTag('div', ['class' => 'col-md-9']) .
                    InputFile::widget([
                        'id' => 'wid'.uniqid(),
                        'language'   => \Yii::$app->language,
                        //'controller' => 'elfinder',
                        //'path' => 'image',
                        'filter'     => 'image',
                        'name'       => 'Settings['.$key.']',
                        'value'      => $item['value'],
                    ])
                    .
                    parent::endTag('div') .
                    parent::endTag('div');
                break;
            case 'menuType':
                $menus = ArrayHelper::map(MenuType::find()->all(), 'id', 'title');
                $return =
                    parent::beginTag('div', ['class' => 'form-group']) .
                    parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                    \Yii::t($traslateCategory, Inflector::camel2words($key)) .
                    parent::endTag('label') .
                    parent::beginTag('div', ['class' => 'col-md-9']) .
                    parent::dropDownList('Settings['.$key.']', $item['value'], $menus, ['class' => 'form-control']).
                    parent::endTag('div') .
                    parent::endTag('div');
                break;
            case 'widgetkit':
                $menus = ArrayHelper::map(Widgetkit::find()->all(), 'id', 'name');
                $return =
                    parent::beginTag('div', ['class' => 'form-group']) .
                    parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                    \Yii::t($traslateCategory, Inflector::camel2words($key)) .
                    parent::endTag('label') .
                    parent::beginTag('div', ['class' => 'col-md-9']) .
                    parent::dropDownList('Settings['.$key.']', $item['value'], $menus, ['class' => 'form-control']).
                    parent::endTag('div') .
                    parent::endTag('div');
                break;
            case 'select':
                $return =
                    parent::beginTag('div', ['class' => 'form-group']) .
                    parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                    \Yii::t($traslateCategory, Inflector::camel2words($key)) .
                    parent::endTag('label') .
                    parent::beginTag('div', ['class' => 'col-md-9']) .
                    parent::dropDownList('Settings['.$key.']', $item['value'], $item['items'], ['class' => 'form-control']).
                    parent::endTag('div') .
                    parent::endTag('div');
                break;
            default:
                $return = '';
                break;
        }
        return $return;
    }
}
