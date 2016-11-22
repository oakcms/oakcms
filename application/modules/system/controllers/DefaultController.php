<?php

namespace app\modules\system\controllers;

use app\components\Controller;

/**
 * Default controller for the `system` module
 */
class DefaultController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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

    public function actionLiveEdit($id)
    {
        \Yii::$app->session->set('oak_live_edit', $id);
        $this->back();
    }
}
