<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\admin\controllers;

use app\modules\admin\widgets\ActiveForm;
use Yii;
use app\modules\admin\models\Modules;
use app\modules\admin\models\search\ModulesSearch;
use app\modules\admin\components\behaviors\StatusController;
use app\components\BackendController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ModulesController implements the CRUD actions for Modules model.
 */
class ModulesController extends BackendController
{
    private $_system_modules = [
        'system',
        'text',
        'user',
        'content',
        'menu',
    ];

    public function actions()
    {
        return [
            'sorting' => [
                'class'          => \app\components\Sorting::className(),
                'query'          => Modules::find(),
                'pk'             => 'module_id',
                'orderAttribute' => 'ordering',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => StatusController::className(),
                'model' => Modules::className(),
            ],
        ];
    }

    /**
     * Lists all Modules models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ModulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'    => $searchModel,
            'dataProvider'   => $dataProvider,
            'system_modules' => $this->_system_modules,
        ]);
    }

    /**
     * Creates a new Modules model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Modules();
        if (Yii::$app->request->isAjax) {
            $model->load(Yii::$app->request->post());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            Yii::$app->cache->flush();
            if (Yii::$app->request->post('submit-type') == 'continue')
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
     * Updates an existing Modules model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->flush();
            if (Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->module_id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Modules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Modules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Modules::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSetting($name)
    {
        $model = Modules::find()->where(['name' => $name])->one();

        if (Yii::$app->request->post('Settings')) {
            $model->setSettings(Yii::$app->request->post('Settings'));
            if ($model->save()) {
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
        if (($model = $this->findModel($id))) {
            $model->settings = '';
            $model->save();
            $this->flash('success', Yii::t('admin', 'Module default settings was restored'));
        } else {
            $this->flash('error', Yii::t('admin', 'Not found'));
        }

        return $this->back();
    }

    /**
     * Deletes an existing Modules model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
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
            if (($model = Modules::findOne($id)) !== null) {
                $model->status = Modules::STATUS_PUBLISHED;
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
        foreach ($id_arr as $id) {
            if (($model = Modules::findOne($id)) !== null) {
                $model->status = Modules::STATUS_DRAFT;
                if (!$model->save()) {
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

        return $this->changeStatus($id, Modules::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        Yii::$app->cache->flush();
        $this->flash('success', Yii::t('admin', 'Module disabled'));

        return $this->changeStatus($id, Modules::STATUS_DRAFT);
    }
}
