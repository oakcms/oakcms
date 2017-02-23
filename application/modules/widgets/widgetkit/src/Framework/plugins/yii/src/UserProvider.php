<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace YOOtheme\Widgetkit\Framework\Yii;

use YOOtheme\Widgetkit\Framework\User\User;
use YOOtheme\Widgetkit\Framework\User\UserProviderInterface;
use app\modules\user\models\User as YiiUser;

class UserProvider implements UserProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($id = null)
    {
        if ($id === null) {
            $id = \Yii::$app->user->id;
        }

        return $this->loadUserBy('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUsername($username)
    {
        return $this->loadUserBy('login', $username);
    }

    /**
     * Loads a user.
     *
     * @param  string $field
     * @param  string $value
     * @return UserInterface
     */
    protected function loadUserBy($field, $value)
    {
        if ($user = YiiUser::findIdentity($value)) {
            return new User(array('id' => $user->id, 'username' => $user->username, 'name' => $user->username, 'email' => $user->email, 'permissions' => ['admin', 'manage_widgetkit']));
        }
    }
}
