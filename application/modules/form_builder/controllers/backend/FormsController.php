<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\controllers\backend;

use app\modules\form_builder\models\FormBuilderField;
use Yii;
use app\modules\form_builder\models\FormBuilderForms;
use app\modules\form_builder\models\search\FormBuilderFormsSearch;
use app\components\BackendController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FormsController implements the CRUD actions for FormBuilderForms model.
 */
class FormsController extends BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all FormBuilderForms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FormBuilderFormsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new FormBuilderForms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FormBuilderForms();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing FormBuilderForms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Update fields
     */
    public function actionUpdateFields($form_id) {
        $success = false;
        $fields = FormBuilderField::find()->where(['form_id' => $form_id])->all();
        if($data = Yii::$app->request->post('data')) {
            $elements = json_decode($data, true);
            foreach ($elements as $element) {
                $fieldModel = new FormBuilderField();

                $fieldModel->form_id    = $form_id;
                $fieldModel->type       = ArrayHelper::getValue($element, 'type', 'text');
                $fieldModel->label      = ArrayHelper::getValue($element, 'label');
                $fieldModel->slug       = ArrayHelper::getValue($element, 'name');
                $fieldModel->data       = json_encode($element);
                $fieldModel->save();
            }
        }

        foreach ($fields as $field) {
            $field->delete();
        }
        $success = ['success' => Yii::t('form_builder', 'Fields saved')];
        return $this->formatResponse($success);
    }

    /**
     * Deletes an existing FormBuilderForms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionPublished()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            if (($model = FormBuilderForms::findOne($id)) !== null) {
                $model->status = FormBuilderForms::STATUS_PUBLISHED;
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
            if (($model = FormBuilderForms::findOne($id)) !== null) {
                $model->status = FormBuilderForms::STATUS_DRAFT;
                $model->save();
            }
        }
        return $this->back();
    }

    public function actionOn($id)
    {
        return $this->changeStatus($id, FormBuilderForms::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, FormBuilderForms::STATUS_DRAFT);
    }

    /**
     * Finds the FormBuilderForms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FormBuilderForms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FormBuilderForms::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
