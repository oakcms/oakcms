<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\language\controllers\backend;

use app\modules\language\models\Language;
use app\modules\language\models\LanguageSource;
use Yii;
use app\modules\language\models\LanguageTranslate;
use app\modules\language\models\search\LanguageTranslateSearch;
use app\components\AdminController;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TranslateController implements the CRUD actions for LanguageTranslate model.
 */
class TranslateController extends AdminController
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
     * Lists all LanguageTranslate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LanguageTranslateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $languages = Language::getLanguages();

        $categories = ArrayHelper::map(
            LanguageSource::find()->select('category')->distinct()->all(),
            'category',
            'category'
        );

        return $this->render('index', [
            'searchModel'    => $searchModel,
            'dataProvider'   => $dataProvider,
            'languages'      => $languages,
            'categories'     => $categories
        ]);
    }

    /**
     * Creates a new LanguageTranslate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LanguageTranslate();

        if(($id = Yii::$app->request->get('id')) !== null) {
            $model->id = $id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->id, 'language' => $model->language]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LanguageTranslate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $language
     * @return mixed
     */
    public function actionUpdate($id, $language)
    {
        $model = $this->findModel($id, $language);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->id, 'language' => $model->language]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LanguageTranslate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $language
     * @return mixed
     */
    public function actionDelete($id, $language)
    {
        $this->findModel($id, $language)->delete();

        return $this->redirect(['index']);
    }


    public function actionOn($id)
    {
        return $this->changeStatus($id, LanguageTranslate::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, LanguageTranslate::STATUS_DRAFT);
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

    /**
     * Finds the LanguageTranslate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $language
     * @return LanguageTranslate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $language)
    {
        if (($model = LanguageTranslate::findOne(['id' => $id, 'language' => $language])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
