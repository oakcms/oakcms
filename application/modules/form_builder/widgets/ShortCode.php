<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\widgets;

use app\modules\form_builder\models\FormBuilder;
use app\modules\form_builder\models\FormBuilderForms;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ShortCode extends \app\components\ShortCode
{
    public static function shortCode($event)
    {
        if(isset($event->output)) {
            $event->output = (new \app\components\ShortCode)->parse(
                'form_builder',
                $event->output,
                function($attrs) {
                    if(is_array($attrs)) {
                        $model = FormBuilderForms::find()->where(['id' => $attrs['id']])->one();
                        if($model === null) {
                            return null;
                        }
                        return self::render(['model' => self::getForm($model)['model']]);
                    }
                    return '';
                }
            );
        }
        return true;
    }

    public static function render($options) {
        return \Yii::$app->getView()->renderFile(__DIR__.'/view/form.php', $options);
    }

    /**
     * @param array $attrs
     * @return string Html for text block
     */
    public static function getForm(&$model) {
        $fields = $model->fields;

        $attributes = [];
        $rulesRequired = [];
        $rulesSafe = [];
        foreach ($fields as $field) {
            $data = Json::decode($field->data);
            $attributes[$field->slug] = $field->label;
            if($required = ArrayHelper::getValue($data, 'required')) {
                $rulesRequired[] = $field->slug;
            } else {
                $rulesSafe[] = $field->slug;
            }
        }

        $formModel = new FormBuilder(array_keys($attributes));
        $formModel->addRule($rulesRequired, 'required');
        $formModel->addRule($rulesSafe, 'safe');
        $formModel->setAttributesLabels($attributes);

        $model->setAttributesFields($attributes);
        $model->setModelForm($formModel);

        return ['model' => $model, 'formModel' => $formModel];
    }
}
