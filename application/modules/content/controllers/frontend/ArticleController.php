<?php

namespace app\modules\content\controllers\frontend;

use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentCategory;
use app\modules\menu\api\Menu;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
{

    public function actionView($catslug, $slug) {

        $categoryModel = ContentCategory::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_category_lang}}.slug' => $catslug])
            ->one();

        $model = ContentArticles::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_articles_lang}}.slug' => $slug])
            ->one();

        if(!$model || !$categoryModel) {
            throw new NotFoundHttpException(\Yii::t('system', 'The requested page does not exist.'));
        }

        return $this->render('view', [
            'model' => $model,
            'categoryModel' => $categoryModel
        ]);
    }
}
