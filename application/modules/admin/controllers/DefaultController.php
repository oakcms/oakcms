<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\user\models\LoginForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\BackendController;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends BackendController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }
}
