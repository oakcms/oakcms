<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\modules\field\models\Field;
use app\modules\field\models\FieldVariant;
use app\modules\field\models\FieldValue;

class AttachFields extends Behavior
{
    public $category_field = null;
    public $categories = array();
    private $fieldVariants = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteValues',
        ];
    }

    public function flush()
    {
        $this->fieldVariants = [];

        $values = FieldValue::find()->where(['item_id' => $this->owner->id])->with('field')->all();

        foreach($values as $value) {
            if($value->variant_id) {
                $this->fieldVariants[$value->field->slug] = FieldVariant::findOne($value->variant_id)->value;
            } else {
                $this->fieldVariants[$value->field->slug] = $value->value;
            }
        }

        return true;
    }

    /**
     * @param string $code
     *
     * @return mixed
     */
    public function getField($code)
    {
        if($this->fieldVariants === null) {
            $this->flush();
        }

        return $this->getFieldValue($code);
    }


    /**
     * @param $code
     *
     * @return mixed
     */
    public function getFieldValue($code)
    {
        if(isset($this->fieldVariants[$code])) {
            return $this->fieldVariants[$code];
        }

        return null;
    }

    public function getFieldValues($code)
    {
        if($field = Field::findOne(['slug' => $code])) {
            if($value = FieldValue::findAll(['field_id' => $field->id, 'item_id' => $this->owner->id])) {
                return ArrayHelper::map($value, 'value', 'value');
            }
        }

        return false;
    }

    public function getFieldVariantId($code)
    {
        if($field = Field::findOne(['slug' => $code])) {
            if($value = FieldValue::findOne(['field_id' => $field->id, 'item_id' => $this->owner->id])) {
                return $value->variant_id;
            }
        }

        return false;
    }

    public function getFieldVariantIds($code)
    {
        if($field = Field::findOne(['slug' => $code])) {
            if($value = FieldValue::findAll(['field_id' => $field->id, 'item_id' => $this->owner->id])) {
                return ArrayHelper::map($value, 'variant_id', 'variant_id');
            }
        }

        return false;
    }

    public function fieldVariants()
    {
        if(!$this->owner->isNewRecord) {
            if(is_array($this->fieldVariants)) {
                return $this->fieldVariants;
            }

            $values = FieldValue::findAll(['item_id' => $this->owner->id]);

            $this->fieldVariants = [];

            foreach($values as $value) {
                $this->fieldVariants[$value->variant_id] = $value->variant_id;
            }

            return $this->fieldVariants;
        } else {
            return [];
        }
    }

    public function getFields()
    {
        $model = $this->owner;

        $fields = Field::find()->where(['relation_model' => $model::className()])->all();

        return $fields;
    }

    public function deleteValues()
    {
        foreach(FieldValue::find()->where(['item_id' => $this->owner->id])->all() as $value) {
            $value->delete();
        }

        return true;
    }
}
