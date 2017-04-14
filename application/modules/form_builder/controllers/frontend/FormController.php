<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */
namespace app\modules\form_builder\controllers\frontend;

use app\modules\form_builder\models\FormBuilder;
use app\modules\form_builder\models\FormBuilderField;
use app\modules\form_builder\models\FormBuilderSubmission;
use app\modules\form_builder\widgets\ShortCode;
use Yii;
use app\modules\form_builder\models\FormBuilderForms;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class FormController extends \app\components\Controller
{

    function actionView($slug) {
        $success = '';
        $model = $this->findModel($slug);

        $models = ShortCode::getForm($model);
        $formModel = $models['formModel'];

        $format = Yii::$app->request->get('format');

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $submission = new FormBuilderSubmission();
            $submission->form_id    = $model->id;
            $submission->created    = time();
            $submission->ip         = Yii::$app->request->userIP;
            $submission->status     = FormBuilderSubmission::STATUS_DRAFT;

            $attachments = [];
            foreach ($models['model']->fields as $field) {
                if($field->type == 'fileInput' && ($attachment = UploadedFile::getInstance($formModel,'rezume'))) {
                    $fieldData = Json::decode($field->data);
                    $uploadsPath = Yii::getAlias($fieldData['destination']);
                    $urlDownload = Yii::getAlias('@webroot');
                    $urlDownload = Yii::getAlias('@web'.str_replace($urlDownload, '', $uploadsPath));
                    if (!file_exists($uploadsPath)) {
                        mkdir($uploadsPath, 0777, true);
                    }
                    $uniqid = uniqid();
                    $fileName = $uploadsPath . '/' . $uniqid .'.'. $attachment->extension;
                    $fileDownload = $urlDownload . '/' . $uniqid .'.'. $attachment->extension;
                    $attachment->saveAs($fileName);
                    $attributes = $formModel->attributes;
                    $attributes[$field->slug] = $fileDownload;
                    $formModel->attributes = $attributes;
                    foreach ($fieldData['attach_file_to'] as $datum) {
                        $attachments[$datum][] = $fileName;
                    }
                }
            }

            $submission->data       = Json::encode($formModel->attributes);
            if($submission->save()) {
                if(ArrayHelper::getValue($model->data, 'email.sendToUser')) {
                    if(($field_id = ArrayHelper::getValue($model->data, 'email.userEmail')) && ($field = FormBuilderField::findOne($field_id))) {
                        $userEmail = $formModel->{$field->slug};
                        $mailer = Yii::$app->mailer->compose()
                            ->setFrom(getenv('ROBOT_EMAIL'))
                            ->setTo($userEmail)
                            ->setSubject(ArrayHelper::getValue($model->data, 'email.userEmailSubject'))
                            ->setHtmlBody($model->parseContent($formModel, 'email.userEmailContent'));

                        foreach ($attachments as $key => $attachment) {
                            if($key == 'useremail') {
                                foreach ($attachment as $filename) {
                                    $mailer->attach($filename);
                                }
                            }
                        }

                        $mailer->send();
                    }
                }

                if(ArrayHelper::getValue($model->data, 'email.sendToAdmin')) {
                    $email = ArrayHelper::getValue($model->data, 'email.adminEmail', getenv('ADMIN_EMAIL'));
                    $email = $model->parseFields($formModel, $email);
                    $email = str_replace(' ', '', $email);
                    $emails = explode(',', $email);

                    $mailer = Yii::$app->mailer
                        ->compose()
                        ->setFrom(getenv('ROBOT_EMAIL'))
                        ->setTo($emails)
                        ->setSubject(ArrayHelper::getValue($model->data, 'email.adminEmailSubject'))
                        ->setHtmlBody($model->parseContent($formModel, 'email.adminEmailContent'));

                    foreach ($attachments as $key => $attachment) {
                        if($key == 'adminemail') {
                            foreach ($attachment as $filename) {
                                $mailer->attach($filename);
                            }
                        }
                    }

                    $mailer->send();
                }

                if($format == 'json') {
                    $success = ArrayHelper::getValue($model->data, 'submission.content');
                } else {
                    $this->flash('success', Yii::t('form_builder', 'Form submited.'));
                }

            } else {
                if($format == 'json') {
                    $this->error = Yii::t('form_builder', 'Email not sending');
                } else {
                    $this->flash('error', Yii::t('form_builder', 'Email not sending.'));
                }
            }
        }
        if($format == 'json') {
            return $this->formatResponse(['success' => $success]);
        }
        return $this->render('view', ['model' => $model]);
    }

    protected function findModel($slug)
    {
        if (($model = FormBuilderForms::find()->where(['slug' => $slug])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Disable Assets
     */
    private function disableAssets()
    {
        Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
        Yii::$app->assetManager->bundles['yii\bootstrap\BootstrapPluginAsset'] = false;
        Yii::$app->assetManager->bundles['yii\web\YiiAsset'] = false;
    }
}
