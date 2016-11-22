<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\controllers;

use yii;
use app\modules\field\models\Field;
use app\modules\field\models\tools\FieldSearch;
use app\modules\field\models\FieldVariant;
use app\modules\field\models\tools\FieldVariantSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

class FieldVariantController extends Controller
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
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        if(yii::$app->request->post('list')) {
            $list = array_map('trim', explode("\n", yii::$app->request->post('list')));

            foreach($list as $variant) {
                $model = new FieldVariant();
                $model->value = htmlspecialchars($variant);
                $model->field_id = (int)yii::$app->request->post('FieldVariant')['field_id'];
                $model->save();
            }

            if(isset($model)) {
                return $this->redirect(['/field/field/update', 'id' => $model->field_id]);
            }
        } else {
            $json = [];
            $model = new FieldVariant();

            $post = yii::$app->request->post('FieldVariant');
            //Если такой вариант уже есть у этого товара, просто выставляем его выделение
            if($have = $model::find()->where(['value' => $post['value'], 'field_id' => $post['field_id']])->one()) {
                $json['result'] = 'success';
                $json['value'] = $have->value;
                $json['id'] = $have->id;
                $json['new'] = false;
            //Если варианта нет, создаем
            } else {
                if ($model->load(yii::$app->request->post()) && $model->save()) {
                    $json['result'] = 'success';
                    $json['value'] = $model->value;
                    $json['id'] = $model->id;
                    $json['new'] = true;
                } else {
                    $json['result'] = 'fail';
                }
            }

            return json_encode($json);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['/field/field/update', 'id' => $model->field_id]);
    }

    protected function findModel($id)
    {
        if (($model = FieldVariant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested field does not exist.');
        }
    }
}
