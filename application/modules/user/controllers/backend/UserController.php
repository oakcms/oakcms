<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\user\controllers\backend;

use app\components\BackendController;
use app\modules\user\forms\LoginForm;
use app\modules\user\models\backend\User;
use Google\Authenticator\GoogleAuthenticator;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class UserController extends BackendController
{
    /**
     * @var \app\modules\user\Module
     */
    public $module;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', ''],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = '//_clear';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(Url::to(['/admin']));
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionLockScreen()
    {
        $this->layout = '//_clear';

        // save current username
        $user = clone Yii::$app->user->identity;

        // force logout
        Yii::$app->user->logout();

        // render form lockscreen
        $model = new LoginForm();
        $model->username = $user->username;    //set default value
        return $this->render('lock-screen', [
            'model' => $model,
            'user' => $user
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAccount()
    {
        $model = User::findOne(\Yii::$app->getUser()->getId());
        $model->scenario = User::SCENARIO_ADMIN_UPDATE;

        if($model->googleAuthenticatorSecret == '') {
            $ga = new GoogleAuthenticator();
            $model->googleAuthenticatorSecret = $ga->generateSecret();
        }

        if ($model->load(Yii::$app->request->post())) {
            if(!$model->googleAuthenticator) {
                $model->googleAuthenticatorSecret = '';
            } else {
                Yii::$app->keyStorage->set('googleAuthenticator', '1');
            }

            if($model->save()) {
                return $this->redirect(['/admin/user/account']);
            }
        }

        return $this->render('account', [
            'model' => $model,
        ]);
    }
}
