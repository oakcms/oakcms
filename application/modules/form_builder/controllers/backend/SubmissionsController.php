<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\controllers\backend;

use Yii;
use app\components\BackendController;
use app\modules\form_builder\models\FormBuilder;
use app\modules\form_builder\models\FormBuilderForms;
use app\modules\form_builder\models\FormBuilderSubmission;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class SubmissionsController extends BackendController
{
    public function actionIndex($form_id = null) {

        if(
            (isset($form_id) && $formModel = (FormBuilderForms::find()->where(['id' => $form_id])->one())) ||
            ($formModel = FormBuilderForms::find()->one())
        ) {
            $searchModel = new FormBuilder(array_keys($formModel->fieldsAttributes));
            $dataProvider = $this->search($formModel->id, $formModel->fieldsAttributes);

            $forms = FormBuilderForms::find()->asArray()->all();
            $arrayForms = [];
            foreach ($forms as $form) {
                $arrayForms[Url::to(['index', 'form_id' => $form['id']])] = $form['title'];
            }

            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'formModel'    => $formModel,
                'arrayForms'   => $arrayForms
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function search($form_id, $fieldsAttributes)
    {
        $submissions = FormBuilderSubmission::find()->where(['form_id' => $form_id])->asArray()->all();

        $arrayData = [];
        foreach ($submissions as $k=>$submission) {
            $data = Json::decode($submission['data']);

            $data['id']         = $submission['id'];
            $data['status']     = $submission['status'];
            $data['ip']         = $submission['ip'];
            $data['created']    = $submission['created'];

            foreach ($data as $key=>$item) {
                $arrayData[$data['id']][(string)$key] = $item;
            }
        }

        $sortAttributes = array_merge(array_map('strval', array_keys($fieldsAttributes)), ['id', 'status', 'ip', 'created']);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $arrayData,
            'pagination' => [
                //'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => $sortAttributes,
            ],
        ]);

        return $dataProvider;
    }

    public function actionDeleteIds()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            $this->findModel($id)->delete();
        }
        return $this->back();
    }

    public function actionPublished()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            if (($model = FormBuilderSubmission::findOne($id)) !== null) {
                $model->status = FormBuilderSubmission::STATUS_PUBLISHED;
                $model->save();
            }
        }

        return $this->back();
    }

    public function actionUnpublished()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            if (($model = FormBuilderSubmission::findOne($id)) !== null) {
                $model->status = FormBuilderSubmission::STATUS_DRAFT;
                $model->save();
            }
        }

        return $this->back();
    }

    protected function findModel($id)
    {
        if (($model = FormBuilderSubmission::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
