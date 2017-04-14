<?php

namespace app\modules\content\models\search;

use app\modules\content\models\ContentPagesLang;
use app\modules\language\models\Language;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\content\models\ContentPages;

/**
 * ContentPagesSearch represents the model behind the search form about `app\modules\content\models\ContentPages`.
 */
class ContentPagesSearch extends ContentPages
{
    public $title;
    public $slug;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status','created_at', 'updated_at', 'parent_id'], 'integer'],
            [['slug', 'title', 'layout'], 'string'],
            [['layout'], 'safe'],
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
    public function search($params, $language = false)
    {
        if(!$language) {
            $language = Language::findOne(Yii::$app->language);
        }

        $query = ContentPages::find()
            ->excludeRoots()
            ->joinWith('translations')
            ->where([ContentPagesLang::tableName().'.language' => $language->language_id])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'lft' => SORT_ASC,
                    'ordering' => SORT_ASC,
                ]
            ]
        ]);

        $this->load($params);

        $dataProvider->sort->attributes['title'] = [
            'asc' => ['{{%content_pages_lang}}.title' => SORT_ASC],
            'desc' => ['{{%content_pages_lang}}.title' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['slug'] = [
            'asc' => ['{{%content_pages_lang}}.slug' => SORT_ASC],
            'desc' => ['{{%content_pages_lang}}.slug' => SORT_DESC],
        ];

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'layout' => $this->layout,
        ]);

        $query->andFilterWhere(['like', 'layout', $this->layout])
            ->andFilterWhere(['like', '{{%content_pages_lang}}.title', $this->title])
            ->andFilterWhere(['like', '{{%content_pages_lang}}.slug', $this->slug]);

        return $dataProvider;
    }
}
