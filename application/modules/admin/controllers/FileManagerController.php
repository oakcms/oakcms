<?php

namespace app\modules\admin\controllers;

class FileManagerController extends \app\components\AdminController
{
    public function actionIndex()
    {
        return $this->render('index');
    } 
}
