<?php

namespace app\modules\content\controllers\backend;

use app\modules\admin\components\behaviors\StatusController;
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
     * Deletes an existing ContentCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

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
