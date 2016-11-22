<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\widgets\types;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Yii;

class Numeric extends \yii\base\Widget
{
    public $model = NULL;
    public $field = null;
    public $options = [];

    public function init()
    {
        \app\modules\field\assets\ValueAsset::register($this->getView());
        parent::init();
    }

    public function run()
    {
        $variantsList = $this->field->variants;

        $variantsList = ArrayHelper::map($variantsList, 'id', 'value');
        $variantsList[0] = '-';
        ksort($variantsList);

        $value = $this->model->getField($this->field->slug);

        $input = Html::input('number', 'choice-field-value', $value, ['data-id' => $this->field->id, 'data-item-id' => $this->model->id, 'class' => 'form-control', 'placeholder' => '']);
        $button = Html::tag('span', Html::button('<i class="glyphicon glyphicon-ok"></i>', ['class' => ' btn btn-success field-save-value']), ['class' => 'input-group-btn']);

        $this->options['class'] .= ' input-group';
        $block = Html::tag('div', $input.$button, $this->options);

        return $block;
    }
}
