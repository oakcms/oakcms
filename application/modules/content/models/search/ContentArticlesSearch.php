<?php

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
            [['id', 'create_user_id', 'update_user_id', 'published_at', 'created_at', 'updated_at', 'status', 'comment_status', 'access_type', 'category_id'], 'integer'],
            [['create_user_ip'], 'safe'],
        ];
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
            $query = ContentArticles::find()->where(['category_id' => $category]);
        } else {
            $query = ContentArticles::find();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'create_user_id' => $this->create_user_id,
            'update_user_id' => $this->update_user_id,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'comment_status' => $this->comment_status,
            'access_type' => $this->access_type,
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['like', 'create_user_ip', $this->create_user_ip]);

        return $dataProvider;
    }
}
