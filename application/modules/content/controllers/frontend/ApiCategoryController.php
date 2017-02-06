<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */
namespace app\modules\content\controllers\frontend;


use app\components\ApiController;
use app\modules\content\models\ContentCategory;
use yii\web\NotFoundHttpException;

class ApiCategoryController extends ApiController
{

    public function actionView($slug) {
        return $this->renderWidgets($this->findModel($slug), 'content');
    }

    protected function findModel($slug)
    {
        if (($model = ContentCategory::find()->published()->joinWith(['translations'])->andWhere(['{{%content_category_lang}}.slug'=>$slug])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
