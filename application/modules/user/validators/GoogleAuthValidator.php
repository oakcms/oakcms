<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\user\validators;

use Google\Authenticator\GoogleAuthenticator;
use yii\validators\Validator;
use Yii;

class GoogleAuthValidator extends Validator
{

    public $secretCodeAttribute = null;

    public $skipOnEmpty = false;
    public $skipOnError = false;
    public $enableClientValidation = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('user', 'Error wrong secret code');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $ga = new GoogleAuthenticator();

        if ($ga->getCode($model->{$this->secretCodeAttribute}) != $value) {
            $this->addError($model, $attribute, $this->message);
        }
    }
}
