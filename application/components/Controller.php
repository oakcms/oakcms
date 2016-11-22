<?php

namespace app\components;

use Yii;
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 08.04.2016
 * Project: oakcms
 * File name: Controller.php
 */
class Controller extends CoreController
{
    public $error;

    public function beforeAction($action)
    {
        Yii::$app->view->on(FrontendView::EVENT_AFTER_RENDER, ['app\modules\widgets\widgets\ShortCode', 'shortCode']);
        Yii::$app->view->on(FrontendView::EVENT_AFTER_RENDER, ['app\modules\text\widgets\ShortCode', 'shortCode']);

        return parent::beforeAction($action);
    }

    public function back()
    {
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Write in sessions alert messages
     * @param string $type error or success
     * @param string $message alert body
     */
    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }

    /**
     * Formats response depending on request type (ajax or not)
     * @param string $success
     * @param bool $back go back or refresh
     * @return mixed $result array if request is ajax.
     */
    public function formatResponse($success = '', $back = true)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if ($this->error) {
                return ['result' => 'error', 'error' => $this->error];
            } else {
                $response = ['result' => 'success'];
                if ($success) {
                    if (is_array($success)) {
                        $response = array_merge(['result' => 'success'], $success);
                    } else {
                        $response = array_merge(['result' => 'success'], ['message' => $success]);
                    }
                }
                return $response;
            }
        } else {
            if ($this->error) {
                $this->flash('error', $this->error);
            } else {
                if (is_array($success) && isset($success['message'])) {
                    $this->flash('success', $success['message']);
                } elseif (is_string($success)) {
                    $this->flash('success', $success);
                }
            }
            return $back ? $this->back() : $this->refresh();
        }
    }
}
