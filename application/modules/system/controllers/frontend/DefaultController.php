<?php

namespace app\modules\system\controllers\frontend;

use app\components\Controller;
use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentPages;
use app\modules\system\models\RecruitmentForm;
use app\modules\system\models\SystemBackCall;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

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

        if (
            $model->load(\Yii::$app->request->post()) &&
            $model->contact($settings['BackCallEmail']['value'], $settings['BackCallSubject']['value'])
        ) {
            $success = [
                'success' => \Yii::t('system', $settings['BackCallSuccessText']['value'])
            ];
        } else {
            $this->error =\Yii::t('system', 'Error');
        }

        return $this->response($success);
    }

    public function actionSendRecruitment()
    {
        $success    = null;
        $model      = new RecruitmentForm();
        $settings   = \Yii::$app->getModule('admin')->getSettings($this->module->id);

        if ($model->load(\Yii::$app->request->post())) {

            $model->resume = UploadedFile::getInstance($model, 'resume');

            if($model->resume) {
                $filename = \Yii::getAlias('@webroot').'/uploads/emailattachments/' . uniqid() . '.' . $model->resume->extension;
                $model->resume->saveAs($filename);
            } else {
                $filename = false;
            }

            if($model->contact($settings['BackCallEmail']['value'], $filename)) {
                $success = [
                    'success' => \Yii::t('system', $settings['BackCallSuccessText']['value'])
                ];
            } else {
                var_dump($model->resume);
                exit;
            }

        }

        return $this->response($success);
    }

    public function actionLiveEdit($id)
    {
        \Yii::$app->session->set('oak_live_edit', $id);
        $this->back();
    }

    public function actionGetTemplate() {
        if($url = \Yii::$app->request->get('template')) {
            // Декодуємо УРЛ
            $url = urldecode($url);

            // Парсимо УРЛ
            $request = parse_url($url);

            $view = '';
            if($request['path'] == 'content/page/view') {
                parse_str($request['query'], $where);

                $page = ContentPages::find()
                    ->joinWith(['translations'])
                    ->andWhere(['{{%content_pages_lang}}.slug' => $where['slug'], 'status' => ContentPages::STATUS_PUBLISHED])
                    ->one();

                $layout = $page->layout;
                $view = 'content/page/'.$layout;
            } elseif($request['path'] == 'content/article/view') {
                parse_str($request['query'], $where);

                $page = ContentArticles::find()
                    ->joinWith(['translations'])
                    ->andWhere(['{{%content_articles_lang}}.slug' => $where['slug'], 'status' => ContentArticles::STATUS_PUBLISHED])
                    ->one();

                $layout = $page->layout;
                $view = 'content/article/'.$layout;
            } else {
                $view = $request['path'];
            }

            return \Yii::$app->controller->renderPartial('//angular/'.$view);
        }
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;

        if ($exception !== null) {

            return $this->render('error', ['exception' => $exception]);
        }
    }
}
