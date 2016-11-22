<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 31.05.2016
 * Project: oakcms
 * File name: ElFinderController.php
 */

namespace app\modules\admin\controllers;

use Yii;
use app\components\AdminController;
use zxbodya\yii2\elfinder\ConnectorAction;

class ElFinderController extends AdminController
{
    public function beforeAction($action)
    {

        if ($action->id === 'manager') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'connector' => array(
                'class' => ConnectorAction::className(),
                'settings' => array(
                    'root' => Yii::getAlias('@webroot') . '/uploads/',
                    'URL' => 'uploads/',
                    'rootAlias' => 'Home',
                    'mimeDetect' => 'none'
                )
            ),
        ];
    }
}
