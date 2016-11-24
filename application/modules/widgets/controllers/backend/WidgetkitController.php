<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 05.09.2016
 * Project: osnovasite
 * File name: Widgetkit.php
 */

namespace app\modules\widgets\controllers\backend;


use app\components\BackendController;
use yii\filters\VerbFilter;

class WidgetkitController extends BackendController
{
    public function beforeAction($action)
    {

        if ($action->id === 'index') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $app = require __DIR__.'/../../widgetkit/widgetkit_yii2.php';
        $app->handle();
    }
}
