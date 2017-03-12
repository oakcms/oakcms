<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */
namespace app\modules\form_builder\controllers\frontend;

use app\modules\form_builder\models\FormBuilder;
use app\modules\form_builder\models\FormBuilderSubmission;
use app\modules\form_builder\widgets\ShortCode;
use Yii;
use app\modules\form_builder\models\FormBuilderForms;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class FormController extends \app\components\Controller
{

    function actionView($slug) {

        $model = $this->findModel($slug);

        $models = ShortCode::getForm($model);
        $formModel = $models['formModel'];

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {

            $submission = new FormBuilderSubmission();
            $submission->form_id    = $model->id;
            $submission->created    = time();
            $submission->ip         = Yii::$app->request->userIP;
            $submission->status     = FormBuilderSubmission::STATUS_DRAFT;
            $submission->data       = Json::encode($formModel->attributes);

            if($submission->save()) {
                $this->flash('success', Yii::t('form_builder', 'Form submited.'));
            }
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
