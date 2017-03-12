<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\form_builder\models\FormBuilderField;

/**
 * FormBuilderFieldSearch represents the model behind the search form about `app\modules\form_builder\models\FormBuilderField`.
 */
class FormBuilderFieldSearch extends FormBuilderField
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'form_id', 'sort'], 'integer'],
            [['type', 'label', 'slug', 'options', 'roles', 'data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = FormBuilderField::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['sort' => SORT_ASC, 'id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 100000,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'form_id' => $this->form_id,
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'roles', $this->roles])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
