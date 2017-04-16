<?php

namespace app\modules\content\controllers\backend;

use app\modules\admin\components\behaviors\SortableModel;
use app\modules\admin\components\behaviors\StatusController;
use app\modules\admin\widgets\ActiveForm;
use app\modules\content\models\search\ContentCategorySearch;
use Yii;
use app\modules\content\models\ContentCategory;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for ContentCategory model.
 */
class CategoryController extends \app\components\CategoryController
{

    public $categoryClass = 'app\modules\content\models\ContentCategory';
    public $moduleName = 'content';
    public $viewRoute = '/article/category';

    public function behaviors()
    {
        return [
            [
                'class' => StatusController::className(),
                'model' => ContentCategory::className()
            ]
        ];
    }

    /**
     * Create form
     *
     * @param null $parent
     * @return array|string|\yii\web\Response
     * @throws \yii\web\HttpException
     */
    public function actionCreate($parent = null)
    {

        $lang = $this->getDefaultLanguage();
        $class = $this->categoryClass;
        $model = new $class;
        $model->language = $lang->language_id;

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else
            {
                $model->status = $class::STATUS_ON;


                $ContentCategory = Yii::$app->request->post('ContentCategory', null);
                if(isset($ContentCategory) && isset($ContentCategory['parent'])) {
                    $parent = (int)$ContentCategory['parent'];
                } else {
                    $parent = null;
                }

                if($parent > 0 && ($parentCategory = $class::findOne($parent))){
                    $model->order = $parentCategory->order;
                    $model->appendTo($parentCategory);
                } else {
                    $model->attachBehavior('sortable', SortableModel::className());
                    $model->makeRoot();
                }

                if(!$model->hasErrors()){
                    $this->flash('success', Yii::t('admin', 'Category created'));
                    return $this->redirect(['/admin/'.$this->moduleName.$this->returnUrl, 'id' => $model->primaryKey]);
                }
                else {
                    $this->flash('error', Yii::t('admin', 'Create error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            return $this->render('create', [
                'model' => $model,
                'parent' => $parent,
                'lang'  => $lang,
                'layouts' => self::getLayouts()
            ]);
        }
    }


    /**
     * Edit form
     *
     * @param $id
     * @return array|string|\yii\web\Response
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id, $language = false)
    {
        $lang = $this->getDefaultLanguage($language);
        $class = $this->categoryClass;

        if(!($model = $class::findOne($id))) {
            return $this->redirect(['/admin/' . $this->moduleName]);
        }
        $model->language = $lang->language_id;

        if ($model->load(Yii::$app->request->post())) {

            if(Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                if($model->save()) {
                    $this->flash('success', Yii::t('admin', 'Category updated'));
                } else {
                    $this->flash('error', Yii::t('admin', 'Update error. {0}', $model->formatErrors()));
                }
                return $this->refresh();
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'lang' => $lang,
                'layouts' => self::getLayouts()
            ]);
        }
    }

    /**
     * @param string $route
     * @return string
     */
    public function actionSelect($route = 'content/category/view', $language = false) {
        $lang = $this->getDefaultLanguage($language);
        Yii::$app->language = $lang->language_id;
        $searchModel = new ContentCategorySearch();
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
            if (($model = ContentCategory::findOne($id)) !== null) {
                $model->status = ContentCategory::STATUS_PUBLISHED;
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
            if (($model = ContentCategory::findOne($id)) !== null) {
                $model->status = ContentCategory::STATUS_DRAFT;
                $model->save();
            }
        }
        return $this->back();
    }

    public function actionOn($id)
    {
        return $this->changeStatus($id, ContentCategory::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, ContentCategory::STATUS_DRAFT);
    }

    /**
     * Get All layouts
     */
    protected static function getLayouts()
    {
        $layouts = [];
        $core = glob(Yii::getAlias('@app/modules/content/views/frontend/category/[!^_]*.php'));
        $template = glob(Yii::getAlias('@frontendTemplate/modules/content/category/[!^_]*.php'));

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
     * Finds the ContentCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContentCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContentCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
