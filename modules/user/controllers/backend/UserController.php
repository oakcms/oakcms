<?php

namespace app\modules\user\controllers\backend;

use app\components\AdminController;
use app\modules\user\models\backend\AccountForm;
use app\modules\user\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class UserController extends AdminController
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = [];

        if (isset($this->module->loginFormBehaviors['oneTimePasswordBehavior'])) {
            $configuration = $this->module->loginFormBehaviors['oneTimePasswordBehavior'];
            if ($configuration['mode'] != \nineinchnick\usr\components\OneTimePasswordFormBehavior::OTP_NONE) {
                if (!isset($configuration['authenticator'])) {
                    $configuration['authenticator'] = \nineinchnick\usr\components\OneTimePasswordFormBehavior::getDefaultAuthenticator();
                }
                // OneTimePasswordAction allows toggling two step auth in user profile
                $actions['toggleOneTimePassword'] = [
                    'class' => '\nineinchnick\usr\components\OneTimePasswordAction',
                    'configuration' => $configuration,
                ];
            }
        }
        return $actions;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = '//_clear';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionProfile()
    {
        $model = Yii::$app->user->identity->userProfile;
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->flash('alert-success', Yii::t('app', 'Your profile has been successfully saved'));
            return $this->refresh();
        }
        return $this->render('profile', ['model' => $model]);
    }

    public function actionAccount()
    {
        $user = Yii::$app->user->identity;
        $model = new AccountForm();
        $model->username = $user->username;
        $model->email = $user->email;
        if ($model->load($_POST) && $model->validate()) {
            $user->username = $model->username;
            $user->email = $model->email;
            if ($model->password) {
                $user->setPassword($model->password);
            }
            $user->save();
            $this->flash('alert-success', Yii::t('backend', 'Your account has been successfully saved'));
            return $this->refresh();
        }
        return $this->render('account', ['model' => $model]);
    }

}
