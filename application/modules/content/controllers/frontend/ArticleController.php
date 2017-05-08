<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\content\controllers\frontend;

use Yii;
use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentCategory;
use yii\helpers\Url;
use app\components\Controller;
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

        if($model === null || $categoryModel === null) {
            throw new NotFoundHttpException(\Yii::t('system', 'The requested page does not exist.'));
        }

        $model->getBehavior('hit')->touch();

        if (Yii::$app->request->isPost && Yii::$app->request->isAjax && Yii::$app->request->post('rating')) {
            $rating = Yii::$app->request->post('rating');
            $res = [];

            /**
             * Обчислити загальний рейтинг з урахуванням змін
             */

            $model->rating_sum += $rating;
            $model->rating_votes++;
            $totalRating = round($model->rating_sum / $model->rating_votes, 2); // округляєм до сотих
            $model->rating = $totalRating;

            $model->save();

            // Записуємо куки
            $cookies = Yii::$app->response->cookies;

            $cookies->add(new \yii\web\Cookie([
                'name' => 'article_rating_'.$model->id,
                'value' => [
                    'article_id'    => $model->id,
                    'rating'        => $model->rating,
                    'rating_sum'    => $model->rating_sum,
                    'rating_votes'  => $model->rating_votes,
                ],
                'expire' => time()+60*60*24*365
            ]));

            //повертаємо новий рейтинг в вид
            $res['rating'] = $totalRating; //передаємо обчислений рейтинг за матеріалом
            $res['ratingVotes'] = $model->rating_votes; //передаємо суму всіх голосів за матеріалом
            return json_encode($res, JSON_NUMERIC_CHECK);
        }

        $breadcrumbs = [];
        if(count($breadcrumbs) == 0) {
            $breadcrumbs = [
                [
                    'label' => $categoryModel->title,
                    'url' => Url::to(['/content/category/view', 'slug' => $categoryModel->slug]),
                ],
                [
                    'label' => $model->title
                ]
            ];
        }

        return $this->render($model->layout, [
            'breadcrumbs' => $breadcrumbs,
            'model' => $model,
            'categoryModel' => $categoryModel
        ]);
    }

    public function actionTag($tag)
    {

        $articles = ContentArticles::find()
            ->published()
            ->joinWith(['tags'])
            ->andWhere(['{{%content_tags}}.name' => $tag])
            ->all();

        return $this->render('tag', ['articles' => $articles]);
    }
}
