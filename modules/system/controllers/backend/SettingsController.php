<?php

namespace app\modules\system\controllers\backend;

use Yii;
use app\modules\system\models\SystemSettings;
use app\components\AdminController;
use yii\helpers\VarDumper;

/**
 * SettingsController implements the CRUD actions for SystemSettings model.
 */
class SettingsController extends AdminController
{
    public function behaviors()
    {
        return [];
    }

    /**
     * Lists all SystemSettings models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->post()) {
            foreach (Yii::$app->request->post() as $param_name=>$param_value) {
                $paramModel = SystemSettings::find()->where(['param_name' => $param_name])->one();
                if($paramModel) {
                    $paramModel->param_value = $param_value;
                    $paramModel->save();
                }
            }
            return $this->renderView();
        } else {
            return $this->renderView();
        }
    }

    /**
     * @return View
     */
    private function renderView()
    {
        $model = SystemSettings::find()->all();
        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
