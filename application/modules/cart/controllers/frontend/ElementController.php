<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\controllers\frontend;

use app\components\Controller;
use app\modules\cart\interfaces\CartElement;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii;

class ElementController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionDelete()
    {
        $success = null;

        $elementId = Yii::$app->request->post('elementId');

        $cart = Yii::$app->cart;

        $elementModel = $cart->getElementById($elementId);

        if($elementModel->delete()) {
            $success = ['message' => Yii::t('cart', 'Element success delete')];
        } else {
            $this->error = Yii::t('cart', 'Element fail delete');
        }

        return $this->formatResponse($success);
    }

    public function actionCreate()
    {
        $success = [];

        $cart = Yii::$app->cart;

        $postData = Yii::$app->request->post();

        $model = $postData['CartElement']['model'];
        if($model && ($productModel = new $model()) instanceof CartElement) {
            $productModel = $productModel::findOne($postData['CartElement']['item_id']);

            $options = [];
            if(isset($postData['CartElement']['options'])) {
                $options = $postData['CartElement']['options'];
            }

            $elementModel = $cart->put($productModel, $postData['CartElement']['count'], $options);

            $success['elementId'] = $elementModel->getId();
            $success['result'] = 'success';
        } else {
            $this->error = Yii::t('cart', 'Empty model '.$model);
        }

        return $this->formatResponse($success);
    }

    public function actionUpdate()
    {
        $json = ['result' => 'undefind', 'error' => false];

        $cart = yii::$app->cart;

        $postData = yii::$app->request->post();

        $elementModel = $cart->getElementById($postData['CartElement']['id']);

        if(isset($postData['CartElement']['count'])) {
            $elementModel->setCount($postData['CartElement']['count'], true);
        }

        if(isset($postData['CartElement']['options'])) {
            $elementModel->setOptions($postData['CartElement']['options'], true);
        }

        $json['elementId'] = $elementModel->getId();
        $json['result'] = 'success';

        return $this->_cartJson($json);
    }

    private function _cartJson($json)
    {
        if ($cartModel = yii::$app->cart) {
            if(!$elementsListWidgetParams = yii::$app->request->post('elementsListWidgetParams')) {
                $elementsListWidgetParams = [];
            }

            $json['elementsHTML'] = \app\modules\cart\widgets\ElementsList::widget($elementsListWidgetParams);
            $json['count'] = $cartModel->getCount();
            $json['clear_price'] = $cartModel->getCount(false);
            $json['price'] = $cartModel->getCostFormatted();
        } else {
            $json['count'] = 0;
            $json['price'] = 0;
            $json['clear_price'] = 0;
        }
        return Json::encode($json);
    }

}
