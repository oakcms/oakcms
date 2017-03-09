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
     * @param $key
     * @param $item
     * @param $translateCategory
     *
     * @return string
     */
    public static function settingField($key, $item, $translateCategory)
    {
        $name               = 'Settings['.$key.']';
        $elementOptions     = ['class' => 'form-control'];
        $value              = ArrayHelper::getValue($item, 'value');
        $items              = ArrayHelper::getValue($item, 'items', []);
        $type               = ArrayHelper::getValue($item, 'type', 'textInput');
        $options            = ArrayHelper::getValue($item, 'options', []);

        if(isset($options['elementOptions'])) {
            $elementOptions = array_merge($elementOptions, $options['elementOptions']);
        } else {

        }
        if(!isset($elementOptions['id'])) {
            $elementOptions['id'] = 'field_'.$key;
        }

        foreach ($item as $k=>$el) {
            if ($el instanceof \Closure) {
                $item[$k] = call_user_func($el);
            }
        }

        switch ($type) {
            case 'checkbox':
                $element = \oakcms\bootstrapswitch\Switcher::widget([
                    'id' => 'wid'.uniqid(),
                    'name' => $name,
                    'checked' => $value
                ]);
                break;
            case 'textInput':
                $element = parent::textInput($name, $value, $elementOptions);
                break;
            case 'textarea':
                $element = parent::textarea($name, $value, $elementOptions);
                break;
            case 'mediaInput':
                $element = InputFile::widget([
                    'id' => 'wid'.uniqid(),
                    'language'   => \Yii::$app->language,
                    'filter'     => 'image',
                    'name'       => $name,
                    'value'      => $value,
                ]);
                break;
            case 'menuType':
                $menus = ArrayHelper::map(MenuType::find()->all(), 'id', 'title');
                $element = parent::dropDownList($name, $value, $menus, $elementOptions);
                break;
            case 'widgetkit':
                $widgets = ArrayHelper::map(Widgetkit::find()->all(), 'id', 'name');
                $element = parent::dropDownList($name, $value, $widgets, $elementOptions);
                break;
            case 'select':
                $element = parent::dropDownList($name, $value, $items, $elementOptions);
                break;
            case 'aceEditor':
                $element = AceEditor::widget([
                    'id'    => $key.'_ace',
                    'mode'  => isset($item['mode']) ? $item['mode']: 'html',
                    'name'  => $name,
                    'value' => $value,
                    'readOnly' => 'false'
                ]);
                break;
            default:
                $element = '';
                break;
        }

        return self::render($key, $item, $element, $elementOptions['id'], $translateCategory);
    }

    /**
     * @param $key
     * @param $item
     * @param $element
     * @param $elementId
     * @param $translateCategory
     *
     * @return string
     */
    protected static function render($key, $item, $element, $elementId, $translateCategory)
    {
        $options            = ['class' => 'form-group'];
        $template           = "{label}\n<div class=\"col-md-9\">{element}\n{hint}</div>";
        $labelOptions       = ['class' => 'col-md-3 control-label'];
        $hintOptions        = ['class' => 'hint-block'];
        $parts              = [];

        $hint    = ArrayHelper::getValue($item, 'hint');

        if(isset($item['options'])) {
            $options = array_merge($options, $item['options']);
        }

        if(isset($options['labelOptions'])) {
            $labelOptions = array_merge($labelOptions, $options['labelOptions']);
        }

        if(isset($options['hintOptions'])) {
            $hintOptions = array_merge($hintOptions, $options['hintOptions']);
        }

        if (!isset($parts['{element}'])) {
            $parts['{element}'] = $element;
        }

        if (!isset($parts['{label}'])) {
            $parts['{label}'] = self::label(\Yii::t($translateCategory, Inflector::camel2words($key)), $elementId, $labelOptions);
        }

        if (!isset($parts['{hint}'])) {
            if($hint !== null) {
                $parts['{hint}'] = self::tag('div', $hint, $hintOptions);
            } else {
                $parts['{hint}'] = '';
            }
        }

        $content = strtr($template, $parts);
        return self::tag('div', $content, $options);
    }
}
