<?php
namespace app\modules\text\controllers\frontend;

use app\components\Controller;
use app\modules\text\models\Text;

class DefaultController extends Controller
{

    public function actionSave()
    {
        $model = Text::find()->where(['slug' => \Yii::$app->request->post('attr')])->one();
        $model->text = \Yii::$app->request->post('data');
        \Yii::$app->cache->flush();
        return $model->save();
    }
}
