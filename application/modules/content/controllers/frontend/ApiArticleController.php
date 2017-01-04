<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 26.12.2016
 * Project: oakcms
 * File name: ApiArticle.php
 */

namespace app\modules\content\controllers\frontend;

use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentCategory;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class ApiArticleController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = 'app\modules\content\models\ContentArticles';

    /**
     * @var array
     */
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => [$this, 'prepareDataProvider']
            ],
//            'view' => [
//                'class' => 'yii\rest\ViewAction',
//                'modelClass' => $this->modelClass,
//                'findModel' => [$this, 'findModel']
//            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ],
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        return new ActiveDataProvider(array(
            'query' => ContentArticles::find()->published()
        ));
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws HttpException
     */
    public function actionView($catslug, $slug)
    {
        $categoryModel = ContentCategory::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_category_lang}}.slug' => $catslug])
            ->one();

        $model = ContentArticles::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_articles_lang}}.slug' => $slug])
            ->asArray()
            ->one();

        if($model === null AND $categoryModel === null) {
            throw new NotFoundHttpException(\Yii::t('system', 'The requested page does not exist.'));
        }

        return $model;
    }

    /**
     * @param string $template
     *
     * @return mixed
     */
    public function actionGetTemplate($template = 'index')
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        return $this->renderPartial($template);
    }
}
