<?php

namespace app\modules\text\controllers\backend;

use app\modules\admin\components\behaviors\StatusController;
use app\modules\admin\widgets\Html;
use app\modules\language\models\Language;
use app\modules\menu\models\MenuType;
use yii\helpers\ArrayHelper;
use Yii;
use app\modules\text\models\Text;
use app\modules\text\models\search\TextSearch;
use app\components\BackendController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for Text model.
 */
class DefaultController extends BackendController
{
    public function actions()
    {
        return [
            'sorting' => [
                'class' => \kotchuprik\sortable\actions\Sorting::className(),
                'query' => Text::find(),
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
                'model' => Text::className()
            ]
        ];
    }

    /**
     * Lists all Text models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TextSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Text model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $lang = $this->getDefaultLanguage();
        $model = new Text();
        $model->slug = Yii::$app->request->get('slug');
        $model->language = $lang->language_id;
        $model->settingsAfterLanguage();

        $positions = require_once Yii::getAlias('@frontendTemplate/positions.php');
        $menus = MenuType::find()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->setSetting(Yii::$app->request->post('Settings'), $model->layout);
            if($model->save()) {
                if(Yii::$app->request->post('submit-type') == 'continue') {
                    return $this->redirect(['update', 'id' => $model->id, 'language' => $lang->url]);
                } else {
                    return $this->redirect(['index']);
                }
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'lang'  => $lang,
                'layouts' => $this->getLayouts(),
                'positions' => $positions,
                'menus' => $menus
            ]);
        }
    }

    /**
     * Updates an existing Text model.
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
        $model->published_at = date('Y-m-d H:i', $model->published_at);

        $positions = require_once Yii::getAlias('@frontendTemplate/positions.php');
        $menus = MenuType::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            $model->setSetting(Yii::$app->request->post('Settings'), $model->layout);
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
                'lang'  => $lang,
                'layouts' => $this->getLayouts(),
                'positions' => $positions,
                'menus' => $menus
            ]);
        }
    }

    /**
     * Deletes an existing Text model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionOn($id)
    {
        return $this->changeStatus($id, Text::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, Text::STATUS_DRAFT);
    }

    public function actionDeleteIds()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            $this->findModel($id)->delete();
        }
        return $this->back();
    }

    public function actionCloneIds()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        Text::batchCopy($id_arr);
        return $this->back();
    }

    public function actionPublished()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            if (($model = Text::findOne($id)) !== null) {
                $model->status = Text::STATUS_PUBLISHED;
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
            if (($model = Text::findOne($id)) !== null) {
                $model->status = Text::STATUS_DRAFT;
                $model->save();
            }
        }
        return $this->back();
    }

    /**
     * Finds the Text model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Text the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Text::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public static function getLayouts($file = null) {

        $files = [];
        if($file) {
            if(is_file($fileL = Yii::getAlias('@frontendTemplate/modules/text/layouts/'.$file.'/plugin.php'))) {
                $require = require $fileL;
                if(is_array($require)) {
                    $files[] = $require;
                }
            } else {
                $require = require Yii::getAlias('@app/modules/text/views/frontend/layouts/'.$file.'/plugin.php');
                if(is_array($require)) {
                    $files[] = $require;
                }
            }
        } else {

            $core = glob(Yii::getAlias('@app/modules/text/views/frontend/layouts/*/plugin.php'));
            $template = glob(Yii::getAlias('@frontendTemplate/modules/text/layouts/*/plugin.php'));

            foreach ($core as $plugin) {
                if(is_file($plugin)) {
                    $require = require $plugin;
                    if(is_array($require)) {
                        $files[$require['name']] = $require;
                    }
                }
            }

            foreach ($template as $plugin) {
                if(is_file($plugin)) {
                    $require = require $plugin;
                    if(is_array($require)) {
                        $files[$require['name']] = $require;
                    }
                }
            }
        }

        return $files;
    }

    public static function getLayoutsResponse($file) {
        if(is_file($fileL = Yii::getAlias('@frontendTemplate/modules/text/layouts/'.$file.'/plugin.php')))
            $plugin = $fileL;
        else
            $plugin = Yii::getAlias('@app/modules/text/views/frontend/layouts/'.$file.'/plugin.php');

        $response = [];
        if(is_file($plugin)) {
            $response = require $plugin;
        }
        return json_encode($response);
    }

    public function actionGetLayout($file) {
        if (Yii::$app->request->isAjax) {
            return self::getLayoutsResponse($file);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetSettings($file, $id = null, $lang = null) {
       if (Yii::$app->request->isAjax) {

            $return = '';

            ($lang === null) ? $lang = Language::findOne(Yii::$app->language) : $lang = Language::findOne($lang);

            $model = Text::findOne($id);
            if($model) {
                $model->language = $lang->language_id;
                $model->settingsAfterLanguage();
            }
            if($id !== null && $model) {
                if($model->settings && is_array($model->settings) && count($model->settings) && $file == $model->layout) {
                    $model->settings = ArrayHelper::merge($this->getLayouts($file)[0]['settings'], $model->settings);
                    foreach ($model->settings as $key=>$setting) {
                        $return .= Html::settingField($key, $setting, 'text');
                    }
                } else {
                    foreach ($this->getLayouts($file)[0]['settings'] as $key=>$setting) {
                        $return .= Html::settingField($key, $setting, 'text');
                    }
                }
            } else {
                foreach ($this->getLayouts($file)[0]['settings'] as $key=>$setting) {
                    $return .= Html::settingField($key, $setting, 'text');
                }
            }

            return $this->renderAjax('get-settings', ['return' => $return]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
