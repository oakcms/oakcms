<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\models\search;

use app\modules\form_builder\models\FormBuilderSubmission;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FormBuilderFormsSearch represents the model behind the search form about `app\modules\form_builder\models\FormBuilderForms`.
 */
class FormBuilderSubmissionsSearch extends FormBuilderSubmission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'ip', 'data'], 'safe'],
            [['status', 'form_id', 'created'], 'integer'],
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
    public function search($params, $form_id)
    {
        $query = FormBuilderSubmission::find()->where(['form_id' => $form_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'form_id' => $this->form_id,
            'created' => $this->created,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
