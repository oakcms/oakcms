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
            if ($action->id=='error')
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

    /*public function actionQuestions()
    {
        return $this->render('questions', []);
    }*/

    /*public function actionAbout()
    {
        return $this->render('about', []);
    }

    public function actionContacts() {
        return $this->render('contacts', []);
    }

    public function actionProjects() {
        return $this->render('projects', []);
    }
    public function actionJobs() {
        return $this->render('jobs', []);
    }

    public function actionHistory() {
        $query = History::find()->published();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 5,
                'forcePageParam' => false,
                'pageSizeParam' => false
            ],
        ]);

        return $this->render('history', ['dataProvider' => $dataProvider]);
    }*/

    public function actionBackCall()
    {
        $success    = null;
        $model      = new SystemBackCall();
        $settings   = \Yii::$app->getModule('admin')->getSettings($this->module->id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            if ($model->contact($settings['BackCallEmail']['value'], $settings['BackCallSubject']['value'])) {

                /*
                $url = 'https://docs.google.com/forms/d/e/1FAIpQLSf0kqkJFjt9booJyMOAVULkIWRUUgiU7nolOOzZaRNMc7RWzQ/formResponse';
                $data = array(); // массив для отправки в гугл форм
                $data['entry.1339533031'] = $model->id;
                $data['entry.692682431'] = $model->name;
                $data['entry.2127229280'] = $model->phone;
                $data['entry.489867667'] = $model->email;
                $data['entry.715720461'] = $model->comment;

                $data = http_build_query($data);

                $options = array( // задаем параметры запроса
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => $data,
                    ),
                );
                $context  = stream_context_create($options); // создаем контекст отправки
                $result = file_get_contents($url, false, $context);

                */

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
            $this->layout = 'yourNewLayout';
            return $this->render('error', ['exception' => $exception]);
        }
    }
}
