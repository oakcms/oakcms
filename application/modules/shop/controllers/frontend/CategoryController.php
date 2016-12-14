<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.12.2016
 * Project: oakcms
 * File name: CategoryController.php
 */

namespace app\modules\shop\controllers\frontend;

use yii\web\NotFoundHttpException;

class CategoryController extends \app\components\Controller
{
    public function actionIndex() {

    }

    public function actionView($slug) {

        return $this->render('view', [
            'model' => self::findModelBySlug($slug)
        ]);
    }

    protected function findModel($id)
    {
        $model = $this->module->getService('category');

        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested product does not exist.');
        }
    }

    protected function findModelBySlug($slug)
    {
        $model = $this->module->getService('category');

        if (($model = $model::findOne(['slug' => $slug])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested product does not exist.');
        }
    }
}
