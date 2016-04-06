<?php

namespace app\modules\user;

/**
 * user module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\user\controllers';

    public $modelMap = [
        'User'                          => 'app\modules\user\models\User',
        'SignupForm'                    => 'app\modules\user\models\SignupForm',
        'ResetPasswordForm'             => 'app\modules\user\models\ResetPasswordForm',
        'PasswordResetRequestForm'      => 'app\modules\user\models\PasswordResetRequestForm',
        'LoginForm'                     => 'app\modules\user\models\LoginForm',
        'EmailConfirmForm'              => 'app\modules\user\models\EmailConfirmForm',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
