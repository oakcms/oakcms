<?php

namespace app\modules\system\controllers\frontend;

use app\components\Controller;
use app\modules\system\models\SystemBackCall;
use yii\helpers\VarDumper;

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

    public function actionMenuRules()
    {
        if (\Yii::$app->hasModule('menu')) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $arr = [];
            foreach (\Yii::$app->menuManager->menuMap->routes as $id => $item) {

                $url = parse_url($item);

                $arrC = explode('/', $url['path']);
                $arrCR = [];
                foreach ($arrC as $i) {
                    $arrCR[] = ucfirst($i);
                }
                $controller = implode('', $arrCR);

                $arr[$id] = [
                    "tempUrls"   => $item,
                    "controller" => $controller,
                ];
            }
            foreach (\Yii::$app->menuManager->menuMap->paths as $id => $item) {
                $arr[$id]['link'] = $id == \Yii::$app->menuManager->menuMap->mainMenu->id ? '' : $item;
            }

            return $arr;
        }
    }

    public function actionGetTemplate() {
        if($url = \Yii::$app->request->get('template')) {
            // Декодуємо УРЛ
            $url = urldecode($url);

            // Парсимо УРЛ
            $request = parse_url($url);

            return \Yii::$app->controller->renderPartial('//angular/'.$request['path']);
        }
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
