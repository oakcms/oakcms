<?php

namespace app\modules\content\models\search;

use app\modules\content\models\ContentCategoryLang;
use app\modules\language\models\Language;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\content\models\ContentCategory;

/**
 * ContentCategorySearch represents the model behind the search form about `app\modules\content\models\ContentCategory`.
 */
class ContentCategorySearch extends ContentCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'tree', 'lft', 'rgt', 'depth', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all'], 'integer'],
            [['icon'], 'safe'],
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
        $query = ContentCategory::find()
            ->joinWith(['translations'])
            ->where([ContentCategoryLang::tableName().'.language' => $language->language_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'lft' => SORT_ASC,
                    'order' => SORT_ASC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            ContentCategory::tableName().'.id' => $this->id,
            ContentCategory::tableName().'.status' => $this->status,
            ContentCategory::tableName().'.created_at' => $this->created_at,
            ContentCategory::tableName().'.updated_at' => $this->updated_at,
            ContentCategory::tableName().'.tree' => $this->tree,
            ContentCategory::tableName().'.lft' => $this->lft,
            ContentCategory::tableName().'.rgt' => $this->rgt,
            ContentCategory::tableName().'.depth' => $this->depth,
            ContentCategory::tableName().'.icon_type' => $this->icon_type,
            ContentCategory::tableName().'.active' => $this->active,
            ContentCategory::tableName().'.selected' => $this->selected,
            ContentCategory::tableName().'.disabled' => $this->disabled,
            ContentCategory::tableName().'.readonly' => $this->readonly,
            ContentCategory::tableName().'.visible' => $this->visible,
            ContentCategory::tableName().'.collapsed' => $this->collapsed,
            ContentCategory::tableName().'.movable_u' => $this->movable_u,
            ContentCategory::tableName().'.movable_d' => $this->movable_d,
            ContentCategory::tableName().'.movable_l' => $this->movable_l,
            ContentCategory::tableName().'.movable_r' => $this->movable_r,
            ContentCategory::tableName().'.removable' => $this->removable,
            ContentCategory::tableName().'.removable_all' => $this->removable_all,
        ]);

        $query->andFilterWhere(['like', ContentCategory::tableName().'.icon', $this->icon]);

        return $dataProvider;
    }
}
