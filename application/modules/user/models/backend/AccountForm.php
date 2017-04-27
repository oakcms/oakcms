<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\user\models\backend;

use Yii;
use yii\base\Model;

/**
 * Account form
 */
class AccountForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_confirm;
    public $googleAuthenticator;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique',
                'targetClass' => '\app\modules\user\models\User',
                'message'     => Yii::t('admin', 'This username has already been taken.'),
                'filter'      => function ($query) {
                 $query->andWhere(['not', ['id'=>Yii::$app->user->id]]);
             }
            ],
            ['username', 'string', 'min' => 1, 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => '\app\modules\user\models\User',
                'message'     => Yii::t('admin', 'This email has already been taken.'),
                'filter'      => function ($query) {
                 $query->andWhere(['not', ['id' => Yii::$app->user->getId()]]);
             }
            ],
            ['password', 'string'],
            [['password_confirm'], 'compare', 'compareAttribute' => 'password'],
            [['googleAuthenticator'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'         => Yii::t('admin', 'Username'),
            'email'            => Yii::t('admin', 'Email'),
            'password'         => Yii::t('admin', 'Password'),
            'password_confirm' => Yii::t('admin', 'Password Confirm'),
        ];
    }
}
