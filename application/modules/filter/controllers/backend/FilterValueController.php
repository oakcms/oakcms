<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\controllers\backend;

use yii;
use app\modules\filter\models\FilterValue;
use app\modules\filter\models\Filter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;


class FilterValueController extends Controller
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
        $model = new FilterValue();

        $json = [];

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
        }

        return json_encode($json);
    }

    public function actionUpdate()
    {
        $post = yii::$app->request->post('FilterValue');

        $model = FilterValue::findOne(['item_id' => $post['item_id'], 'filter_id' => $post['filter_id']]);

        if(!$model) {
            $model = new FilterValue;
        } else {
            $filter = Filter::findOne($model->filter_id);
            if($filter->type == 'radio') {
                FilterValue::deleteAll(['item_id' => $post['item_id'], 'filter_id' => $post['filter_id']]);
                $model = new FilterValue;
            }
        }

        $json = [];

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
        }

        return json_encode($json);
    }

    public function actionDelete()
    {
        $itemId = yii::$app->request->post('item_id');
        $variantId = yii::$app->request->post('variant_id');
        $filterId = yii::$app->request->post('filter_id');

        if($value = FilterValue::find()->where(['item_id' => $itemId, 'variant_id' => $variantId])->one()) {
            $value->delete();
        } else {
            FilterValue::deleteAll(['item_id' => $itemId, 'filter_id' => $filterId]);
        }

        return json_encode(['result' => 'success']);
    }

}
