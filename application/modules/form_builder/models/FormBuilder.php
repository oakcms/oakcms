<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\models;


use yii\base\DynamicModel;

class FormBuilder extends DynamicModel
{
    public $attributeLabels = [];

    public function setAttributesLabels($attributes)
    {
        foreach ($attributes as $name=>$label) {
            $this->attributeLabels[$name] = $label;
        }
    }

    public function attributeLabels() {
        return $this->attributeLabels;
    }
}
