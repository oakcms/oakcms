<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\menu\models;


use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class MenuItemSearch represents the model behind the search form about `app\common\models\Menu`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuItemSearch extends MenuItem
{
    /**
     * @var bool
     */
    public $excludeRoots = true;
    /**
     * @var integer
     */
    public $excludeItem;

    /**
     * @var integer
     */
    public $main;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'menu_type_id', 'parent_id', 'status', 'main', 'link_type', 'secure', 'created_at', 'updated_at', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock', 'excludeItem', 'excludeRoots'], 'integer'],
            [['language', 'title', 'alias', 'path', 'note', 'link', 'link_params', 'layout_path', 'access_rule', 'metakey', 'metadesc', 'robots'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MenuItem::find();
        $query->with(['menuType']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'lft' => SORT_ASC,
                    'ordering' => SORT_ASC
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            if ($this->excludeRoots) {
                $query->excludeRoots();
            }

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'menu_type_id' => $this->menu_type_id,
            'parent_id' => $this->parent_id,
            'link_type' => $this->link_type,
            'secure' => $this->secure,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'lft' => $this->lft,
            'rgt' => $this->rgt,
            'level' => $this->level,
            'ordering' => $this->ordering,
            'hits' => $this->hits,
            'lock' => $this->lock,
        ]);

        if($this->main <= 0) {
            $query->andFilterWhere(['status' => $this->status]);
        } else {
            $query->andFilterWhere(['status' => $this->main]);
        }

        $query->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'link_params', $this->link_params])
            ->andFilterWhere(['like', 'layout_path', $this->layout_path])
            ->andFilterWhere(['like', 'access_rule', $this->access_rule])
            ->andFilterWhere(['like', 'metakey', $this->metakey])
            ->andFilterWhere(['like', 'metadesc', $this->metadesc])
            ->andFilterWhere(['like', 'robots', $this->robots]);

        if ($this->excludeRoots) {
            $query->excludeRoots();
        }

        if ($this->excludeItem && $item = MenuItem::findOne($this->excludeItem)) {
            /** @var $item MenuItem */
            $query->excludeItem($item);
        }

        return $dataProvider;
    }
}
