<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\user\models\LoginForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\AdminController;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends AdminController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionFlushcache()
    {
        Yii::$app->cache->flush();
        $this->flash('success', Yii::t('easyii', 'Cache flushed'));
        return $this->back();
    }
}
