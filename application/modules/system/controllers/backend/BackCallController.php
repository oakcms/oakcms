<?php

namespace app\modules\system\controllers\backend;

use Yii;
use app\modules\system\models\SystemBackCall;
use yii\data\ActiveDataProvider;
use app\components\BackendController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BackCallController implements the CRUD actions for SystemBackCall model.
 */
class BackCallController extends BackendController
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
     * Lists all SystemBackCall models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => SystemBackCall::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SystemBackCall model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SystemBackCall();

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
     * Updates an existing SystemBackCall model.
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
     * Deletes an existing SystemBackCall model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes items an existing SeoItems model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
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
            if (($model = SystemBackCall::findOne($id)) !== null) {
                $model->status = SystemBackCall::ST_PROCESSED;
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
            if (($model = SystemBackCall::findOne($id)) !== null) {
                $model->status = SystemBackCall::ST_NEW;
                $model->save();
            }
        }
        return $this->back();
    }

    public function actionOn($id)
    {
        return $this->changeStatus($id, SystemBackCall::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, SystemBackCall::STATUS_DRAFT);
    }

    /**
     * Finds the SystemBackCall model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SystemBackCall the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SystemBackCall::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
