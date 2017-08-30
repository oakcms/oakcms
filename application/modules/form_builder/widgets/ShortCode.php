<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\widgets;

use app\modules\form_builder\components\validators\ReCaptchaValidator;
use app\modules\form_builder\models\FormBuilder;
use app\modules\form_builder\models\FormBuilderForms;
use app\modules\form_builder\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ShortCode extends \app\components\ShortCode
{
    public static function shortCode($event)
    {
        if(isset($event->output)) {
            $return = (new \app\components\ShortCode)->parse(
                'form_builder',
                $event->output,
                function($attrs) {
                    if(is_array($attrs)) {
                        $model = FormBuilderForms::find()->where(['id' => $attrs['id']])->one();
                        if($model === null) {
                            return null;
                        }

                        return self::render($model);
                    }
                    return '';
                }
            );

            $event->output = $return;
        }
        return true;
    }

    public static function render($model) {
        if(!Module::$htmlFormSuccess) {
            return \Yii::$app->getView()->renderFile(__DIR__.'/view/form.php', self::getForm($model));
        }
        return Module::$htmlFormSuccess;
    }

    /**
     * @param $model
     * @return array
     */
    public static function getForm(&$model) {
        $fields = $model->fields;

        $attributes = [];
        $rulesRequired = [];
        $rulesSafe = [];
        $recaptcha = [];
        $recaptcha_secret = '';
        foreach ($fields as $field) {
            $data = Json::decode($field->data);
            $attributes[$field->slug] = $field->label;
            if($required = ArrayHelper::getValue($data, 'required')) {
                $rulesRequired[] = $field->slug;
            } elseif (
                $recaptcha_pub = ArrayHelper::getValue($data, 'recaptcha_api_key') &&
                $recaptcha_secret = ArrayHelper::getValue($data, 'recaptcha_api_key_secret')
            ) {
                $recaptcha = $field;
            } else {
                $rulesSafe[] = $field->slug;
            }
        }

        $formModel = new FormBuilder(array_keys($attributes));
        $formModel->addRule($rulesRequired, 'required');
        $formModel->addRule($rulesSafe, 'safe');

        if(count($recaptcha) > 0) {
            $formModel->addRule($recaptcha->slug,  ReCaptchaValidator::className(), ['secret' => $recaptcha_secret]);
        }

        $formModel->setAttributesLabels($attributes);

        $model->setAttributesFields($attributes);
        $model->setModelForm($formModel);

        return ['model' => $model, 'formModel' => $formModel];
    }
}
