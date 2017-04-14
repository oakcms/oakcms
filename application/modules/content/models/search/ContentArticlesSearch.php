<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\content\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\content\models\ContentArticles;

/**
 * ContentArticlesSearch represents the model behind the search form about `app\modules\content\models\ContentArticles`.
 */
class ContentArticlesSearch extends ContentArticles
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_user_id', 'update_user_id', 'created_at', 'updated_at', 'status', 'comment_status', 'access_type', 'category_id'], 'integer'],
            [['title', 'slug', 'published_at'], 'string'],
            [['create_user_ip'], 'safe'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if(isset($behaviors['slug'])) unset($behaviors['slug']);
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $category = null)
    {
        if($category) {
            $query = ContentArticles::find()
                ->joinWith(['translations'])
                ->where(['category_id' => $category, '{{%content_articles_lang}}.language' => Yii::$app->language]);
        } else {
            $query = ContentArticles::find()
                ->joinWith(['translations'])
                ->where(['{{%content_articles_lang}}.language' => Yii::$app->language]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'title' => [
                    'asc' => ['{{%content_articles_lang}}.title' => SORT_ASC],
                    'desc' => ['{{%content_articles_lang}}.title' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'slug' => [
                    'asc' => ['{{%content_articles_lang}}.slug' => SORT_ASC],
                    'desc' => ['{{%content_articles_lang}}.slug' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'category_id',
                'published_at'
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'create_user_id' => $this->create_user_id,
            'update_user_id' => $this->update_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'comment_status' => $this->comment_status,
            'access_type' => $this->access_type,
            'category_id' => $this->category_id,
        ]);

        if (
            !is_null($this->published_at) &&
            strpos($this->published_at, ' - ') !== false
        ) {
            list($start_date, $end_date) = explode(' - ', $this->published_at);
            $query->andFilterWhere(['between', 'published_at', strtotime($start_date), strtotime($end_date)+86399]);
        }

        $query
            ->andFilterWhere(['like', '{{%content_articles_lang}}.title', $this->title])
            ->andFilterWhere(['like', '{{%content_articles_lang}}.slug', $this->slug])
            ->andFilterWhere(['like', 'create_user_ip', $this->create_user_ip]);

        return $dataProvider;
    }
}
