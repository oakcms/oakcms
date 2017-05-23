<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\controllers\backend;

use app\components\BackendController;
use app\modules\shop\models\Modification;
use app\modules\shop\models\Product;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ModificationController extends BackendController
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

    public function actionAddPopup($productId)
    {
        $this->layout = 'mini';

        $model = new Modification();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            yii::$app->session->setFlash('modification-success-added', 'Модификация успешно добавлена', false);
            return '<script>parent.document.location = "'.Url::to(['/admin/shop/product/update', 'id' => $model->product_id]).'";</script>';
        }

        $model->product_id = (int)$productId;
        $model->available = 'yes';

        $productModel = Product::findOne($productId);

        if (!$productModel) {
            throw new NotFoundHttpException('The requested product does not exist.');
        }

        return $this->render('create', [
            'model' => $model,
            'productModel' => $productModel
        ]);
    }

    public function actionCreate()
    {
        $model = new Modification();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Yii::$app->request->referrer);
        }

        $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            $productModel = $model->product;

            return $this->render('update', [
                'productModel' => $productModel,
                'module' => $this->module,
                'model' => $model,
            ]);
        }
    }

    public function actionSort() {
        $positions = \Yii::$app->request->post();
        $productId = \Yii::$app->request->post('productId');

        foreach ($positions as $order => $id) {
            if ($target = Modification::find()->where(['id' => $id, 'product_id' => $productId])->one()) {
                $target->updateAttributes(['sort' => intval($order)]);
            }
        }

        $this->formatResponse(['success' => Yii::t('shop', 'Modifications soted')]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->formatResponse(['success' => Yii::t('shop', 'Modifications deleted')]);
    }

    public function actionEditField()
    {
        $name = Yii::$app->request->post('name');
        $value = Yii::$app->request->post('value');
        $pk = unserialize(base64_decode(Yii::$app->request->post('pk')));
        Modification::editField($pk, $name, $value);
    }

    protected function findModel($id)
    {
        if (($model = Modification::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
