<?php

namespace app\modules\user;

/**
 * user module definition class
 */
class Module extends \yii\base\Module
{
    public $settings = [
        'title' => [
            'type' => 'textInput',
            'value' => 'OAKCMS'
        ]
    ];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\user\controllers';

    public function adminMenu() {
        return [
            'label' => \Yii::t('user', 'User'),
            'icon' => '<i class="fa fa-user"></i>',
            'url' => ['/admin/user']
        ];
    }

    /**
     * @var array Extra behaviors to attach to the login form. If the views are overridden in a theme
     * this can be used to placed extra logic. @see FormModelBehavior
     */
    public $loginFormBehaviors;

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
