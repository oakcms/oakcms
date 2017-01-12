<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 22.11.2016
 * Project: kardamon_blog
 * File name: search.php
 */

namespace app\modules\content\controllers\frontend;


use app\components\Controller;
use app\modules\content\models\ContentArticles;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{

    public function actionSearch() {

        $q = \Yii::$app->request->get('q');
        if(isset($q) && $q != '' && strlen($q) > 3) {
            $dataProvider = new ActiveDataProvider([
                'query' => ContentArticles::find()
                    ->published()
                    ->joinWith(['translations'])
                    ->andFilterWhere([
                        'or',
                        ['like', '{{%content_articles_lang}}.title', $q],
                        ['like', '{{%content_articles_lang}}.description', $q],
                        ['like', '{{%content_articles_lang}}.content', $q]
                    ])
                    ->orderBy(['published_at'=>SORT_DESC]),
                'pagination' => [
                    'defaultPageSize' => 10,
                    'forcePageParam' => false,
                    'pageSizeParam' => false,
                ],
            ]);

            return $this->render('view', [
                'dataProvider' => $dataProvider
            ]);
        }
    }
}
