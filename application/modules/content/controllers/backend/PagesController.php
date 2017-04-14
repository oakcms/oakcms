<?php

namespace app\modules\content\controllers\backend;

use app\modules\admin\components\behaviors\StatusController;
use app\modules\system\models\DbState;
use Guzzle\Inflection\Inflector;
use Yii;
use app\modules\content\models\ContentPages;
use app\modules\content\models\search\ContentPagesSearch;
use app\components\BackendController;
use yii\base\Response;
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
            [
                'class' => StatusController::className(),
                'model' => ContentPages::className()
            ]
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

        if ($model->load(Yii::$app->request->post())) {
            $model->saveNode(true);
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
     * Updates an existing ContentPages model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $language = false)
    {
        $lang = $this->getDefaultLanguage($language);
        $model = $this->findModel($id);
        $model->language = $lang->language_id;
        $model->settingsAfterLanguage();


        if ($model->load(Yii::$app->request->post())) {
            $model->setSetting(Yii::$app->request->post('Settings'));
            if($model->saveNode(true)) {
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
     * @return Response
     */
    public function actionSorting()
    {
        $data = \Yii::$app->request->post('sorting');

        foreach ($data as $order => $id) {
            if ($target = ContentPages::findOne($id)) {
                $target->updateAttributes(['ordering' => intval($order)]);
            }
        }

        ContentPages::find()->roots()->one()->reorderNode('ordering');
        DbState::updateState(ContentPages::tableName());
    }

    /**
     * @param string $route
     * @return string
     */
    public function actionSelect($route = 'content/page/view', $language = false) {
        $lang = $this->getDefaultLanguage($language);
        Yii::$app->language = $lang->language_id;
        $searchModel = new ContentPagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $lang);

        Yii::$app->getView()->applyModalLayout();

        return $this->render('select', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'route' => $route,
            'lang' => $lang
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
        if(($model = $this->findModel($id))){
            $children = $model->children()->all();
            $model->deleteWithChildren();
            foreach($children as $child) {
                $child->afterDelete();
            }
        } else {
            $this->error = Yii::t('admin', 'Not found');
        }
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
     * Get All layouts
     */
    protected static function getLayouts()
    {
        $layouts = [];
        $core = glob(Yii::getAlias('@app/modules/content/views/frontend/page/[!_]*.php'));
        $template = glob(Yii::getAlias('@frontendTemplate/modules/content/page/[!_]*.php'));

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

    public function actionSystem() {
        $model = ContentPages::findOne(1);
        var_dump($model->reorderNode('lft'));
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
