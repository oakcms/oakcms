<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\shop\controllers;

use app\modules\shop\models\Outcoming;
use app\modules\shop\models\Product;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class OutcomingController extends Controller
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

    public function actionCreate()
    {
        $model = new Outcoming();

        if ($post = Yii::$app->request->post()) {
            $model->date = time();
            $model->content = serialize($post);

            $flash = '';
            foreach($post['element'] as $id => $count) {
                if($product = Product::findOne($id)) {
                    $answer = $product->minusAmount($count, true);
                    if($answer != 1){
                        $flash .= $product->name.' '.$answer.'<br/>';
                        \Yii::$app->session->setFlash('success', $answer);
                    }
                }
            }

            if($flash != '') {
                \Yii::$app->session->setFlash('success', $flash);
            } else if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Отправление успешно добавлено.');
            }else {
                \Yii::$app->session->setFlash('success', 'Что-то пошло не так.Попробуйте еще раз.');
            }

            return $this->redirect(['create', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
}
