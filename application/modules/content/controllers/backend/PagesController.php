<?php

namespace app\modules\content\controllers\backend;

use Yii;
use app\modules\content\models\ContentPages;
use app\modules\content\models\search\ContentPagesSearch;
use app\components\BackendController;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PagesController implements the CRUD actions for ContentPages model.
 */
class PagesController extends BackendController
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
     * Lists all ContentPages models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContentPagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ContentPages model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $lang = $this->getDefaultLanguage();
        $model = new ContentPages();
        $model->language = $lang->language_id;
        $model->settingsAfterLanguage();
        $model->setScenario('insert');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'lang'  => $lang
            ]);
        }
    }

    /**
     * Updates an existing ContentPages model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $language)
    {
        $lang = $this->getDefaultLanguage($language);
        $model = $this->findModel($id);
        $model->language = $lang->language_id;
        $model->settingsAfterLanguage();
        $model->setScenario('update');


        if ($model->load(Yii::$app->request->post())) {
            $model->setSetting(Yii::$app->request->post('Settings'));
            if($model->save()) {
                if(Yii::$app->request->post('submit-type') == 'continue') {
                    return $this->redirect(['update', 'id' => $model->id, 'language' => $lang->url]);
                } else {
                    return $this->redirect(['index']);
                }
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'lang' => $lang,
            ]);
        }
    }

    /**
     * @param string $route
     * @return string
     */
    public function actionSelect($route = 'grom/page/frontend/default/view') {
        $searchModel = new ContentPagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        Yii::$app->layout = '//modal';

        return $this->render('select', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'route' => $route
        ]);
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
            if (($model = ContentPages::findOne($id)) !== null) {
                $model->status = ContentPages::STATUS_PUBLISHED;
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
            if (($model = ContentPages::findOne($id)) !== null) {
                $model->status = ContentPages::STATUS_DRAFT;
                $model->save();
            }
        }
        return $this->back();
    }

    /**
     * Deletes an existing ContentPages model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteImage($id) {
        $model = $this->findModel($id);
        $model->background_image = '';
        if($model->save()) {
            @unlink($model->getUploadPath('background_image'));
            @unlink($model->getThumbUploadPath('background_image'));
        }
        return $this->back();
    }


    public function actionOn($id)
    {
        return $this->changeStatus($id, ContentPages::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, ContentPages::STATUS_DRAFT);
    }

    /**
     * Finds the ContentPages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContentPages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContentPages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
