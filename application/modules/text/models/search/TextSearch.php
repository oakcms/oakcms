<?php

namespace app\modules\text\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\text\models\Text;

/**
 * TextSearch represents the model behind the search form about `app\modules\text\models\Text`.
 */
class TextSearch extends Text
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['title', 'subtitle', 'layout', 'slug', 'text'], 'safe'],
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
    public function search($params)
    {
        $query = Text::find()
            ->joinWith(['translations']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order' => SORT_ASC]]
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'title' => [
                    'asc' => ['{{%texts_lang}}.title' => SORT_ASC],
                    'desc' => ['{{%texts_lang}}.title' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'slug',
                'layout',
                'status'
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', '{{%texts_lang}}.title', $this->title])
            ->andFilterWhere(['like', 'subtitle', $this->subtitle])
            ->andFilterWhere(['like', 'layout', $this->layout])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
