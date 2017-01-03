<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.09.2016
 * Project: osnovasite
 * File name: PageController.php
 */

namespace app\modules\content\controllers\frontend;


use app\components\Controller;
use app\modules\content\models\ContentPages;
use app\modules\menu\api\Menu;
use app\modules\menu\models\MenuItems;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class PageController extends Controller
{

    public function actionView($slug, $slugMenu = null) {

        $model = $this->findModel($slug);

        return $this->render('view', ['model' => $model]);
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
