<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\shop\controllers\frontend;

use app\modules\shop\models\Product;
use yii\web\NotFoundHttpException;

class ProductController extends \app\components\Controller
{
    public function actionView($slug) {

        return $this->render('view', [
            'model' => self::findModelBySlug($slug)
        ]);
    }

    protected function findModel($id)
    {

        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested product does not exist.');
        }
    }

    protected function findModelBySlug($slug)
    {
        if (($model = Product::findOne(['slug' => $slug])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested product does not exist.');
        }
    }
}
