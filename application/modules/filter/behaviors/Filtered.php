<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\behaviors;

use app\modules\filter\models\Filter;
use app\modules\filter\models\FilterValue;
use app\modules\filter\models\FilterVariant;
use yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;

class Filtered extends Behavior
{
    public $fieldName = 'filter';

    public function option($key, $value, $sign = '=')
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $filter = Filter::findOne(['slug' => $key]);

        if (!$filter) {
            throw new \yii\base\Exception('Filter do not find');
        }

        $numeric_value = (int)current($value);

        if ($sign == '=') {
            $variants = FilterVariant::findAll(['filter_id' => $filter->id, 'value' => $value]);
        } elseif ($sign == '>') {
            $variants = FilterVariant::find()->where('filter_id = :filter_id AND numeric_value > :value', [':filter_id' => $filter->id, ':value' => $numeric_value])->all();
        } else {
            $variants = FilterVariant::find()->where('filter_id = :filter_id AND numeric_value < :value', [':filter_id' => $filter->id, ':value' => $numeric_value])->all();
        }

        $filterIds = [];

        foreach ($variants as $variant) {
            $filterIds[$filter->id][] = $variant->id;
        }

        if (empty($filterIds)) {
            return $this->owner->andWhere(['id' => 0]);
        }

        return $this->filtered($filterIds, 2);
    }

    public function filtered($filterIds = false, $mode = 0)
    {
        if (!$filterIds) {
            $filterIds = Yii::$app->request->get($this->fieldName);
        }

        if (empty($filterIds)) {
            return $this->owner;
        }

        $condition = ['OR'];
        $variantCount = 0;
        $filterCount = count($filterIds);

        foreach ($filterIds as $filterId => $value) {
            $filter = Filter::findOne($filterId);
            if ($filter->type == 'range' && is_string($value)) {
                $value = explode(';', $value);
                if ($value[0] != $value[1]) {
                    $variants = FilterVariant::find()->where('filter_id = :filterId AND (numeric_value >= :min AND numeric_value <= :max)', [':filterId' => $filterId, ':min' => $value[0], ':max' => $value[1]])->select('id')->all();
                } else {
                    $variants = FilterVariant::find()->where('filter_id = :filterId AND numeric_value = :value', [':filterId' => $filterId, ':value' => $value[0]])->select('id')->all();
                }
                $variantIds = ArrayHelper::map($variants, 'id', 'id');
            } else {
                $variantIds = $value;
            }

            $condition[] = ['filter_id' => $filterId, 'variant_id' => $variantIds];

            if ($mode == 1) {
                $variantCount += count($variantIds);
            } else {
                $variantCount++;
            }

        }

        $filtered = FilterValue::find()->select('item_id')->groupBy('item_id')->andHaving("COUNT(DISTINCT `filter_id`) = $variantCount")->andFilterWhere($condition);

        if ($filtered->count() > 0) {
            $this->owner->andWhere(['id' => $filtered]);
        } else {
            $this->owner->andWhere(['id' => 0]);
        }

        return $this->owner;
    }
}
