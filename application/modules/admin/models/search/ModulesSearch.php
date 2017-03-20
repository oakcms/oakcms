<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Modules;

/**
 * ModulesSearch represents the model behind the search form about `app\modules\admin\models\Modules`.
 */
class ModulesSearch extends Modules
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'isFrontend', 'isAdmin', 'order', 'status'], 'integer'],
            [['name', 'class', 'controllerNamespace', 'viewPath', 'BackendControllerNamespace', 'AdminViewPath', 'title', 'icon', 'settings'], 'safe'],
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
        $query = Modules::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order' => SORT_ASC]]
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
