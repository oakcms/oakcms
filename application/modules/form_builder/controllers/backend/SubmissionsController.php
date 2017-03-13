<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\controllers\backend;


use app\components\BackendController;
use app\modules\form_builder\models\FormBuilder;
use app\modules\form_builder\models\FormBuilderForms;
use app\modules\form_builder\models\FormBuilderSubmission;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class SubmissionsController extends BackendController
{
    public function actionIndex($form_id = null) {

        if(isset($form_id)) {
            $formModel = FormBuilderForms::find()->where(['id' => $form_id, 'status' => FormBuilderForms::STATUS_PUBLISHED])->one();
        } else {
            $formModel = FormBuilderForms::find()->where(['status' => FormBuilderForms::STATUS_PUBLISHED])->one();
        }

        $searchModel = new FormBuilder(array_keys($formModel->fieldsAttributes));

        $dataProvider = $this->search($formModel->id, $formModel->fieldsAttributes);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'formModel'    => $formModel
        ]);
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
                $arrayData[$k][(string)$key] = $item;
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
}
