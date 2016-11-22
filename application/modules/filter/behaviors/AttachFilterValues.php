<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\modules\filter\models\Filter;
use app\modules\filter\models\FilterValue;

class AttachFilterValues extends Behavior
{
    private $filterVariants = null;
    private $filterOptions = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteValues',
        ];
    }

    public function checkedId($id)
    {
        $variantFilters = $this->filterVariants();

        if(isset($variantFilters[$id])) {
            return true;
        } else {
            return false;
        }
    }

    public function getOption($code, $full = false)
    {
        if(isset($this->filterOptions[$code]) && is_array($this->filterOptions[$code])) {
            return $this->filterOptions[$code];
        }

        if($filter = Filter::findOne(['slug' => $code])) {
            $values = FilterValue::findAll(['filter_id' => $filter->id, 'item_id' => $this->owner->id]);

            foreach($values as $value) {
                if($full) {
                    $this->filterOptions[$filter->slug][] = $value->variant;
                } else {
                    $this->filterOptions[$filter->slug][] = $value->variant->value;
                }
            }
        }

        if(!isset($this->filterOptions[$code])) {
            return [];
        }

        return $this->filterOptions[$code];
    }

    public function filterVariants()
    {
        if(!$this->owner->isNewRecord) {
            if(is_array($this->filterVariants)) {
                return $this->filterVariants;
            }

            $values = FilterValue::findAll(['item_id' => $this->owner->id]);

            $this->filterVariants = [];

            foreach($values as $value) {
                $this->filterVariants[$value->variant_id] = $value->variant_id;
            }

            return $this->filterVariants;
        } else {
            return [];
        }
    }

    public function getFilters()
    {
        $model = $this->owner;
        $return = [];
        $filters = Filter::find()->all();

        foreach($filters as $filter) {
            $field = $filter->relation_field_name;
            $show = false;
            if (empty($filter->relation_field_value)) {
                $show = true;
            } else {
                foreach ($filter->relation_field_value as $rfv) {
                    if ($model->{$field} == $rfv) {
                        $show = true;
                    }
                }
            }

            if ($show == true) {
                $return[] = $filter;
            }
        }

        return $return;
    }

    public function getOptions()
    {
        $return = [];
        $variantFilters = $this->owner->filterVariants();

        foreach($this->owner->filters as $filter) {
            foreach($filter->variants as $variant) {
                if(isset($variantFilters[$variant->id])) {
                    $return[$filter->name][] = $variant->value;
                }
            }
        }

        return $return;
    }

    public function getFilterData()
    {
        return "data-filter-variants='".json_encode($this->filterVariants())."'";
    }

    public function deleteValues()
    {
        foreach(FilterValue::find()->where(['item_id' => $this->owner->id])->all() as $value) {
            $value->delete();
        }

        return true;
    }
}
