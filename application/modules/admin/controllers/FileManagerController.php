<?php

namespace app\modules\admin\controllers;

class FileManagerController extends \app\components\BackendController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
