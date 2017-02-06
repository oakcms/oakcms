<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.09.2016
 * Project: osnovasite
 * File name: PageController.php
 */

namespace app\modules\content\controllers\frontend;


use app\components\ApiController;
use app\modules\content\models\ContentPages;
use yii\web\NotFoundHttpException;

class ApiPageController extends ApiController
{

    public function actionView($slug) {
        return $this->renderWidgets($this->findModel($slug), 'content');
    }

    protected function findModel($slug)
    {
        if (($model = ContentPages::find()->published()->joinWith(['translations'])->andWhere(['{{%content_pages_lang}}.slug'=>$slug])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
