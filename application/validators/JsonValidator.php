<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\validators;

use yii\validators\Validator;
use Yii;

class JsonValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('admin', '"{attribute}" must be a valid JSON');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateValue($value)
    {
        if (!@json_decode($value)) {
            return [$this->message, []];
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = Yii::$app->getI18n()->format($this->message, [
            'attribute' => $model->getAttributeLabel($attribute)
        ], Yii::$app->language);
        return <<<"JS"
            try {
                JSON.parse(value);
            } catch (e) {
                messages.push('{$message}')
            }
JS;
    }
}
