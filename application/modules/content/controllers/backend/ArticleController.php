<?php

namespace app\modules\content\controllers\backend;

use app\modules\admin\components\behaviors\StatusController;
use app\modules\content\models\ContentTags;
use Yii;
use app\modules\content\models\ContentArticles;
use app\modules\content\models\search\ContentArticlesSearch;
use app\components\BackendController;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for ContentArticles model.
 */
class ArticleController extends BackendController
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
            [
                'class' => StatusController::className(),
                'model' => ContentArticles::className()
            ]
        ];
    }

    public function actionList()
    {
        $query = Yii::$app->request->get('query');
        $models = ContentTags::find()->where(['name' => $query])->all();
        $items = [];

        foreach ($models as $model) {
            $items[] = ['name' => $model->name];
        }
        // We know we can use ContentNegotiator filter
        // this way is easier to show you here :)
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $items;
    }

    /**
     * Lists all ContentArticles models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        $searchModel = new ContentArticlesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all ContentArticles Category models.
     * @return mixed
     */
    public function actionCategory($id)
    {
        $searchModel = new ContentArticlesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ContentArticles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $lang = $this->getDefaultLanguage();
        $model = new ContentArticles();
        $model->language = $lang->language_id;
        $model->settingsAfterLanguage();
        $model->setScenario('insert');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->id, 'language' => $lang->url]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'lang'  => $lang,
                'layouts' => self::getLayouts()
            ]);
        }
    }

    /**
     * Updates an existing ContentArticles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string|boolean $language
     * @return mixed
     */

    public function actionUpdate($id, $language = false)
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
        }
        return $this->render('update', [
            'model' => $model,
            'lang' => $lang,
            'layouts' => self::getLayouts()
        ]);
    }

    /**
     * @param string $route
     * @return string
     */
    public function actionSelect($route = 'content/article/view')
    {
        $searchModel = new ContentArticlesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        Yii::$app->getView()->applyModalLayout();

        return $this->render('select', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'route' => $route
        ]);
    }

    /**
     * Deletes an existing ContentArticles model.
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
            if (($model = ContentArticles::findOne($id)) !== null) {
                $model->status = ContentArticles::STATUS_PUBLISHED;
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
            if (($model = ContentArticles::findOne($id)) !== null) {
                $model->status = ContentArticles::STATUS_DRAFT;
                $model->save();
            }
        }
        return $this->back();
    }

    public function actionOn($id)
    {
        return $this->changeStatus($id, ContentArticles::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, ContentArticles::STATUS_DRAFT);
    }

    /**
     * Get All layouts
     */
    protected static function getLayouts()
    {
        $layouts = [];
        $core = glob(Yii::getAlias('@app/modules/content/views/frontend/article/[^_]*.php'));
        $template = glob(Yii::getAlias('@frontendTemplate/modules/content/article/[^_]*.php'));

        foreach ($core as $layout) {
            if(is_file($layout)) {
                $layouts[basename($layout, ".php")] = \yii\helpers\Inflector::camel2words(basename($layout, ".php"));
            }
        }

        foreach ($template as $layout) {
            if(is_file($layout)) {
                $layouts[basename($layout, ".php")] = \yii\helpers\Inflector::camel2words(basename($layout, ".php"));
            }
        }

        return $layouts;
    }

    /**
     * Finds the ContentArticles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContentArticles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContentArticles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
