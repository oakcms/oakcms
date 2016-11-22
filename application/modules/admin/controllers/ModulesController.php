<?php

namespace app\modules\admin\controllers;

use app\modules\admin\widgets\ActiveForm;
use himiklab\sortablegrid\SortableGridAction;
use Yii;
use app\modules\admin\models\ModulesModules;
use app\modules\admin\models\search\ModulesModulesSearch;
use app\modules\admin\components\behaviors\StatusController;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ModulesController implements the CRUD actions for ModulesModules model.
 */
class ModulesController extends AdminController
{
    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => ModulesModules::className(),
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => StatusController::className(),
                'model' => ModulesModules::className()
            ]
        ];
    }

    /**
     * Lists all ModulesModules models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ModulesModulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ModulesModules model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ModulesModules();
        if(Yii::$app->request->isAjax) {
            $model->load(Yii::$app->request->post());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            Yii::$app->cache->flush();
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->module_id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ModulesModules model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->flush();
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->module_id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionSetting($name)
    {
        $model = ModulesModules::find()->where(['name' => $name])->one();

        if (Yii::$app->request->post('Settings')) {
            $model->setSettings(Yii::$app->request->post('Settings'));
            if($model->save()){
                $this->flash('alert-success', Yii::t('admin', 'Module settings updated'));

                if (Yii::$app->request->post('submit-type') == 'continue')
                    return $this->redirect(['settings', 'id' => $model->id]);
                else
                    return $this->redirect(['index']);
            } else {
                $this->flash('error', Yii::t('easyii', Yii::t('easyii', 'Update error. {0}', $model->formatErrors())));
                return $this->redirect(['settings', 'id' => $model->id]);
            }
        } else {
            return $this->render('settings', [
                'model' => $model,
            ]);
        }
    }

    public function actionRestoreSettings($id)
    {
        if(($model = $this->findModel($id))){
            $model->settings = '';
            $model->save();
            $this->flash('success', Yii::t('app', 'Module default settings was restored'));
        } else {
            $this->flash('error', Yii::t('app', 'Not found'));
        }
        return $this->back();
    }

    /**
     * Deletes an existing ModulesModules model.
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
        foreach($id_arr as $id) {
            if (($model = ModulesModules::findOne($id)) !== null) {
                $model->status = ModulesModules::STATUS_PUBLISHED;
                $model->save();
            }
        }
        Yii::$app->cache->flush();
        return $this->back();
    }

    public function actionUnpublished()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach($id_arr as $id) {
            if (($model = ModulesModules::findOne($id)) !== null) {
                $model->status = ModulesModules::STATUS_DRAFT;
                if(!$model->save()) {
                    print_r($model->getErrors());
                    exit;
                }
            }
        }
        Yii::$app->cache->flush();
        return $this->back();
    }

    public function actionOn($id)
    {
        Yii::$app->cache->flush();
        $this->flash('success', Yii::t('admin', 'Module enabled'));
        return $this->changeStatus($id, ModulesModules::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        Yii::$app->cache->flush();
        $this->flash('success', Yii::t('admin', 'Module disabled'));
        return $this->changeStatus($id, ModulesModules::STATUS_DRAFT);
    }

    /**
     * Finds the ModulesModules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModulesModules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ModulesModules::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
