<?php

namespace app\modules\content\controllers\frontend;

use app\modules\content\models\ContentArticles;
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
                ->andWhere(['category_id' => $model->id])
                ->orderBy(['published_at'=>SORT_DESC])
                ->published(),

            'pagination' => [
                'defaultPageSize' => 10,
                'forcePageParam' => false,
                'pageSizeParam' => false,
            ],
        ]);

        $breadcrumbs = [];

        return $this->render('view', [
            'breadcrumbs' => $breadcrumbs,
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }
}
