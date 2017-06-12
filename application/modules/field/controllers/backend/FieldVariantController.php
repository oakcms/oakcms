<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\controllers\backend;

use Yii;
use app\modules\field\models\FieldVariant;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
        $post = Yii::$app->request->post('FieldVariant');
        $list = ArrayHelper::getValue($post, 'list', '');

        if($list == '' || $list) {
            $list = array_map('trim', explode("\n", $list));
            $list = array_diff($list, array(''));

            foreach($list as $variant) {
                $model = new FieldVariant();
                $model->value = htmlspecialchars($variant);
                $model->field_id = (int) ArrayHelper::getValue(Yii::$app->request->post('FieldVariant'), 'field_id');
                $model->save();
            }

            if(isset($model)) {
                return $this->redirect(['/admin/field/field/update', 'id' => $model->field_id]);
            } else {
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else {
            $json = [];
            $model = new FieldVariant();


            //Если такой вариант уже есть у этого товара, просто выставляем его выделение
            $value = ArrayHelper::getValue($post, 'value');
            if(
                !empty($value) &&
                ($have = $model::find()->where(['value' => $value, 'field_id' => ArrayHelper::getValue($post, 'field_id')])->one())
            ) {
                $json['result'] = 'success';
                $json['value'] = $have->value;
                $json['id'] = $have->id;
                $json['new'] = false;
            //Если варианта нет, создаем
            } else {
                if ($model->load($post) && $model->save()) {
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

        return $this->redirect(['/admin/field/field/update', 'id' => $model->field_id]);
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
