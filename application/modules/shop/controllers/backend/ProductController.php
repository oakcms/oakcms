<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\controllers\backend;

use app\components\BackendController;
use app\modules\gallery\behaviors\AttachImages;
use app\modules\gallery\models\Image;
use app\modules\shop\events\ProductEvent;
use app\modules\shop\models\Modification;
use app\modules\shop\models\modification\ModificationSearch;
use app\modules\shop\models\Price;
use app\modules\shop\models\price\PriceSearch;
use app\modules\shop\models\PriceType;
use app\modules\shop\models\Product;
use app\modules\shop\models\product\ProductSearch;
use app\modules\shop\models\stock\StockSearch;
use app\modules\shop\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ProductController extends BackendController
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
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete'    => ['post'],
                    'edittable' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id)
    {

        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->addModification(Yii::$app->request->post(), $model);
            $productEvent = new ProductEvent(['model' => $model]);
            $this->module->trigger(Module::EVENT_PRODUCT_CREATE, $productEvent);
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model'      => $model
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $typeParams = Yii::$app->request->queryParams;
        $typeParams['StockSearch']['product_id'] = $id;
        $StockSearch = new StockSearch();
        $StockDataProvider = $StockSearch->search($typeParams);

        $searchModel = new PriceSearch();
        $typeParams = Yii::$app->request->queryParams;
        $typeParams['PriceSearch']['product_id'] = $id;
        $dataProvider = $searchModel->search($typeParams);

        $modificationModel = new ModificationSearch();
        $searchModificationModel = new ModificationSearch();
        $typeParams['ModificationSearch']['product_id'] = $id;

        $modificationDataProvider = $searchModificationModel->search($typeParams);

        if (
            $model->load(Yii::$app->request->post()) &&
            $this->addModification(Yii::$app->request->post(), $model) &&
            $model->save()
        ) {
            /** @var Module $module */
            $module = $this->module;
            $productEvent = new ProductEvent(['model' => $model]);

            if ($prices = yii::$app->request->post('Price')) {
                foreach ($prices as $typeId => $price) {
                    $type = PriceType::findOne($typeId);
                    $price = new Price($price);
                    $price->type_id = $typeId;
                    $price->name = $type->name;
                    $price->sort = $type->sort;
                    $price->product_id = $model->id;
                    $price->save();
                }
            }

            $this->module->trigger($module::EVENT_PRODUCT_UPDATE, $productEvent);

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'modificationModel'        => $modificationModel,
                'searchModificationModel'  => $searchModificationModel,
                'modificationDataProvider' => $modificationDataProvider,
                'model'                    => $model,
                'module'                   => $this->module,
                'dataProvider'             => $dataProvider,
                'searchModel'              => $searchModel,
                'StockSearch'              => $StockSearch,
                'StockDataProvider'        => $StockDataProvider
            ]);
        }
    }

    /**
     * @param $post
     * @param $model Product
     * @return boolean
     */
    private function addModification($post, &$model)
    {
        $countProducts = count($post['variants']['mainImageSlug']);

        $changeImage = false;

        for ($i = 0; $i < $countProducts; $i++) {

            if(isset($post['changeImage'][$i]) AND $post['changeImage'][$i] != '') {
                $changeImage = true;
            }

            if(isset($post['variants']['id'][$i]) AND $post['variants']['id'][$i] != '') {
                $modification = Modification::findOne($post['variants']['id'][$i]);
            } else {
                $modification = new Modification();
            }

            $modification->detachBehavior('images');

            $modification->product_id       = $model->id;
            $modification->price            = $post['variants']['price'][$i];
            $modification->code             = $post['variants']['code'][$i];
            $modification->available        = $post['variants']['available'][$i];
            $modification->name             = $post['variants']['name'][$i];
            $modification->amount           = $post['variants']['amount'][$i];
            $modification->filter_values    = serialize(ArrayHelper::getValue($post, 'variants.filter_values.' . $i, []));
            $modification->sort             = $i;

            if(!$modification->save()) {
                $model->addErrors($modification->getErrors());
                return false;
            }

            $photoUpload = UploadedFile::getInstanceByName('image'.$i);

            if ($photoUpload) {
                $uploadsPath = Yii::getAlias(Yii::$app->getModule('gallery')->imagesStorePath.'/');
                if (!file_exists($uploadsPath)) {
                    mkdir($uploadsPath, 0777, true);
                }

                $photoUpload->saveAs("{$uploadsPath}/{$photoUpload->baseName}.{$photoUpload->extension}");

                $modification->attachBehavior('images', [
                    'class' => AttachImages::className(),
                    'mode' => 'single',
                ]);

                foreach ($modification->getImages() as $image) {
                    $image->delete();
                }

                $modification->attachImage("{$uploadsPath}/{$photoUpload->baseName}.{$photoUpload->extension}");
                $this->flash('success', 'Image change');
            } else {
                if(!$changeImage && $post['variants']['mainImageSlug'][$i] != '') {

                    $image = Image::find()->where([
                        'urlAlias' => $post['variants']['mainImageSlug'][$i],
                    ])->one();

                    if($image) {
                        $newImage = new Image();

                        $newImage->filePath     = $image->filePath;
                        $newImage->itemId       = $modification->id;
                        $newImage->modelName    = $image->modelName;
                        $newImage->urlAlias     = $image->urlAlias;
                        if($newImage->save()) {
                            $this->flash('success', 'Image change');
                        } else {
                            $this->flash('error', 'Image not changed');
                        }
                    }
                }
            }
        }
        return true;
    }

    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            $this->findModel($id)->delete();

            $module = $this->module;
            $productEvent = new ProductEvent(['model' => $model]);
            $this->module->trigger($module::EVENT_PRODUCT_DELETE, $productEvent);
        }

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
            $model = $this->findModel($id);
            $model->delete();

            $productEvent = new ProductEvent(['model' => $model]);
            $this->module->trigger(Module::EVENT_PRODUCT_DELETE, $productEvent);
        }
        return $this->back();
    }

    public function actionProductInfo()
    {
        $productCode = (int)yii::$app->request->post('productCode');

        if ($model = Product::find()->where('code=:code OR id=:code', [':code' => $productCode])->one()) {
            $json = [
                'status' => 'success',
                'name'   => $model->name,
                'code'   => $model->code,
                'id'     => $model->id,
            ];
        } else {
            $json = [
                'status'  => 'fail',
                'message' => yii::t('order', 'Not found'),
            ];
        }

        die(json_encode($json));
    }
}
