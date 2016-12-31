<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.12.2016
 * Project: oakcms
 * File name: CategoryController.php
 */

namespace app\modules\shop\controllers\frontend;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CategoryController extends \app\components\Controller
{
    public function actionIndex() {

    }

    public function actionView($slug) {
        $model = self::findModelBySlug($slug);

        $productDataProvider = new ActiveDataProvider([
            'query' => $model->getProducts(),
            'sort' => new \yii\data\Sort([
                'attributes' => [
                    'price',
                    'is_promo',
                    'is_popular',
                    'is_new',
                ],
            ]),
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'productDataProvider' => $productDataProvider,
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
