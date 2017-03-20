<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\user\models\backend;

use app\modules\user\Module;
use app\modules\user\validators\GoogleAuthValidator;
use yii\helpers\ArrayHelper;

class User extends \app\modules\user\models\User
{
    const SCENARIO_ADMIN_CREATE = 'adminCreate';
    const SCENARIO_ADMIN_UPDATE = 'adminUpdate';
    const SCENARIO_ADMIN_CREATE_INIT = 'adminCreateInit';

    public $newPassword;
    public $newPasswordRepeat;
    public $googleAuthSecretCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['newPassword', 'newPasswordRepeat'], 'required', 'on' => self::SCENARIO_ADMIN_CREATE],
            [['newPassword'], 'required', 'on' => self::SCENARIO_ADMIN_CREATE_INIT],
            ['newPassword', 'string', 'min' => 6],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
            ['googleAuthSecretCode', 'string', 'max' => 6],
            [['googleAuthSecretCode'], GoogleAuthValidator::className(), 'secretCodeAttribute' => 'googleAuthenticatorSecret', 'when' => function($model) {
                return $model->googleAuthenticator == 1;
            }],
        ]);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADMIN_CREATE] = ['username', 'email', 'status', 'role', 'newPassword', 'newPasswordRepeat'];
        $scenarios[self::SCENARIO_ADMIN_CREATE_INIT] = ['username', 'email', 'status', 'role', 'newPassword'];
        $scenarios[self::SCENARIO_ADMIN_UPDATE] = ['username', 'email', 'status', 'role', 'newPassword', 'newPasswordRepeat', 'googleAuthenticatorSecret', 'googleAuthenticator', 'googleAuthSecretCode'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'newPassword' => \Yii::t('user', 'User new password'),
            'newPasswordRepeat' => \Yii::t('user', 'User repeat password'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->newPassword)) {
                $this->setPassword($this->newPassword);
            }

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $auth = \Yii::$app->authManager;
        $auth->revokeAll($this->id);
        $auth->assign($auth->getRole($this->role), $this->id);
    }
}
