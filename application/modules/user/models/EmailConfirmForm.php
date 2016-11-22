<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: EmailConfirmForm.php
 */

namespace app\modules\user\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use app\modules\user\models\User;
use Yii;

class EmailConfirmForm extends Model
{
    /**
     * @var \app\modules\user\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param  string $token
     * @param  array $config
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Отсутствует код подтверждения.');
        }
        $this->_user = User::findByEmailConfirmToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Неверный токен.');
        }
        parent::__construct($config);
    }

    /**
     * Confirm email.
     *
     * @return boolean if email was confirmed.
     */
    public function confirmEmail()
    {
        $user = $this->_user;
        $user->status = User::S_ACTIVE;
        $user->removeEmailConfirmToken();

        return $user->save();
    }
}