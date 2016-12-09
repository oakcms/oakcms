<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\controllers\backend;

use yii;
use app\modules\field\models\FieldValue;
use app\modules\field\models\Field;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;


class FieldValueController extends Controller
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
                    'create' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new FieldValue();

        $json = [];

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
            $json['error'] = $model->getErrors();
        }

        return json_encode($json);
    }

    public function actionUpdate()
    {
        $post = yii::$app->request->post('FieldValue');

        $model = FieldValue::findOne(['item_id' => $post['item_id'], 'field_id' => $post['field_id']]);

        if(!$model) {
            $model = new fieldValue;
        } else {
            $field = field::findOne($model->field_id);
            if($field->type == 'radio') {
                FieldValue::deleteAll(['item_id' => $post['item_id'], 'field_id' => $post['field_id']]);
                $model = new fieldValue;
            }
        }

        $json = [];

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
            $json['error'] = $model->getErrors();
        }

        return json_encode($json);
    }

    public function actionDelete()
    {
        $itemId = yii::$app->request->post('item_id');
        $variantId = yii::$app->request->post('variant_id');
        $fieldId = yii::$app->request->post('field_id');

        if($value = fieldValue::find()->where(['item_id' => $itemId, 'variant_id' => $variantId])->one()) {
            $value->delete();
        } else {
            FieldValue::deleteAll(['item_id' => $itemId, 'field_id' => $fieldId]);
        }

        return json_encode(['result' => 'success']);
    }

}
