<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 08.06.2016
 * Project: oakcms
 * File name: Html.php
 */

namespace app\modules\admin\widgets;


use yii\helpers\VarDumper;

class Html extends \yii\bootstrap\Html
{

    /**
     * @param $item
     * @return string
     */
    public static function settingField($key, $item, $traslateCategory)
    {
        $return = '';
        switch ($item['type'] ){

            case 'checkbox':
                $return =

                    parent::beginTag('div', ['class' => 'form-group']) .
                        parent::beginTag('label', ['class' => 'col-md-3 control-label']) .
                            \Yii::t($traslateCategory, $key) .
                        parent::endTag('label') .
                        parent::beginTag('div', ['class' => 'col-md-9']) .
                            parent::hiddenInput('Settings['.$key.']', 0) .
                            \oakcms\bootstrapswitch\Switcher::widget([
                                'name' => 'Settings['.$key.']',
                                'checked' => $item['value']
                            ]) .
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
