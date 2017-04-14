<?php

namespace app\modules\text\models\search;

use app\modules\text\models\TextsLang;
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
            ->joinWith(['translations'])
            ->andWhere([TextsLang::tableName().'.language' => Yii::$app->language]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'title' => [
                    'asc' => [TextsLang::tableName().'.title' => SORT_ASC],
                    'desc' => [TextsLang::tableName().'.title' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'slug',
                'layout',
                'status',
                'order'
            ],
            'defaultOrder' => [
                'order' => SORT_ASC
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

        $query->andFilterWhere(['like', TextsLang::tableName().'.title', $this->title])
            ->andFilterWhere(['like', TextsLang::tableName().'.subtitle', $this->subtitle])
            ->andFilterWhere(['like', Text::tableName().'.layout', $this->layout])
            ->andFilterWhere(['like', Text::tableName().'.slug', $this->slug])
            ->andFilterWhere(['like', TextsLang::tableName().'.text', $this->text]);

        return $dataProvider;
    }
}
