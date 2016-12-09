<?php

namespace app\modules\system\controllers\frontend;

use app\components\Controller;
use app\modules\system\models\History;
use app\modules\system\models\SystemBackCall;
use yii\data\ActiveDataProvider;

/**
 * Default controller for the `system` module
 */
class DefaultController extends Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            // change layout for error action
            if ($action->id == 'error')
                $this->layout = '//_clear';
            return true;
        } else {
            return false;
        }
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', []);
    }

    public function actionBackCall()
    {
        $success    = null;
        $model      = new SystemBackCall();
        $settings   = \Yii::$app->getModule('admin')->getSettings($this->module->id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            if ($model->contact($settings['BackCallEmail']['value'], $settings['BackCallSubject']['value'])) {

                $success = [
                    'success' => \Yii::t('system', $settings['BackCallSuccessText']['value'])
                ];
            }
        }

        return $this->formatResponse($success);
    }

    public function actionLiveEdit($id)
    {
        \Yii::$app->session->set('oak_live_edit', $id);
        $this->back();
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            //$this->layout = 'error';
            return $this->render('error', ['exception' => $exception]);
        }
    }
}
