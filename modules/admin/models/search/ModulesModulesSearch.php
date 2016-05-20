<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\ModulesModules;

/**
 * ModulesModulesSearch represents the model behind the search form about `app\modules\admin\models\ModulesModules`.
 */
class ModulesModulesSearch extends ModulesModules
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'isFrontend', 'isAdmin', 'order', 'status'], 'integer'],
            [['name', 'class', 'controllerNamespace', 'viewPath', 'AdminControllerNamespace', 'AdminViewPath', 'title', 'icon', 'settings'], 'safe'],
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
        $query = ModulesModules::find();

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
            'module_id' => $this->module_id,
            'isFrontend' => $this->isFrontend,
            'isAdmin' => $this->isAdmin,
            'order' => $this->order,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'controllerNamespace', $this->controllerNamespace])
            ->andFilterWhere(['like', 'viewPath', $this->viewPath])
            ->andFilterWhere(['like', 'AdminControllerNamespace', $this->AdminControllerNamespace])
            ->andFilterWhere(['like', 'AdminViewPath', $this->AdminViewPath])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'settings', $this->settings]);

        return $dataProvider;
    }
}
