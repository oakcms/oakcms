<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\controllers\backend;

use app\modules\shop\models\Price;
use app\modules\shop\models\price\PriceSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class PriceController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'edittable' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($modification_id)
    {
        $model = new Price();
        Yii::$app->getView()->applyModalLayout();
        $searchModel = new PriceSearch();
        $typeParams = Yii::$app->request->queryParams;
        $typeParams['PriceSearch']['modification_id'] = $modification_id;
        $dataProvider = $searchModel->search($typeParams);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model = new Price();
        }

        return $this->render('index', [
            'model'             => $model,
            'modification_id'   => $modification_id,
            'dataProvider'      => $dataProvider,
            'searchModel'       => $searchModel
        ]);
    }

    public function actionCreate()
    {
        $model = new Price();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //nothing
        }

        $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $this->redirect(Yii::$app->request->referrer);
    }

    public function actionEditField()
    {
        $name = Yii::$app->request->post('name');
        $value = Yii::$app->request->post('value');
        $pk = unserialize(base64_decode(Yii::$app->request->post('pk')));
        Price::editField($pk, $name, $value);
    }

    protected function findModel($id)
    {
        if (($model = Price::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
