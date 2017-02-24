<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\relations\controllers\backend;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii;

class DefaultController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles
                    ]
                ]
            ]
        ];
    }

    public function actionList($model, $limit = 100)
    {
        $this->layout = 'mini';

        $model = "\\$model";
        $model = new $model();
        $modelList = $model::find();

        $modelList = $modelList->limit($limit);

        if($s = yii::$app->request->post('s')) {
            $modelList = $modelList->andWhere(['LIKE', 'name', $s])->orWhere(['id' => $s]);
            foreach($this->module->fields as $field) {
                $modelList = $modelList->orWhere(['LIKE', $field, $s]);
            }
        }

        return $this->render('list', ['modelList' => $modelList->all(), 'fields' => $this->module->fields]);
    }
}
