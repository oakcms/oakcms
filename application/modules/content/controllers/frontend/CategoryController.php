<?php

namespace app\modules\content\controllers\frontend;

use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentArticlesLang;
use app\modules\content\models\ContentCategory;
use app\modules\menu\api\Menu;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;

class CategoryController extends Controller
{

    public function actionView($slug) {
        $model = ContentCategory::find()
            ->joinWith(['translations'])
            ->where(['{{%content_category_lang}}.slug' => $slug])
            ->one();

        if($model === null) {
            throw new NotFoundHttpException(\Yii::t('system', 'The requested page does not exist.'));
        }

        $dataProvider = new ActiveDataProvider([
            'query' => ContentArticles::find()
                ->joinWith(['translations'])
                ->andWhere([
                    ContentArticles::tableName().'.category_id' => $model->id,
                    ContentArticlesLang::tableName().'.language' => \Yii::$app->language
                ])
                ->orderBy(['published_at' => SORT_DESC])
                ->published(),

            'pagination' => [
                'defaultPageSize' => 12,
                'forcePageParam' => false,
                'pageSizeParam' => false,
            ],
        ]);

        $breadcrumbs = [];

        return $this->render($model->layout, [
            'breadcrumbs' => $breadcrumbs,
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }
}
