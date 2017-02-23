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
use kartik\select2\Select2;
use Yii;

class Select extends \yii\base\Widget
{
    public $model = NULL;
    public $field = null;
    public $options = [];

    public function init()
    {
        \app\modules\field\assets\VariantsAsset::register($this->getView());
        parent::init();
    }

    public function run()
    {
        $variantsList = $this->field->variants;

        $variantsList = ArrayHelper::map($variantsList, 'id', 'value');
        $variantsList[0] = '-';
        ksort($variantsList);

        $checked = $this->model->getFieldVariantId($this->field->slug);

        $select = Select2::widget([
            'name' => 'choice-field-value',
            'value' => $checked,
            'data' => $variantsList,
            'language' => 'ru',
            'options' => ['placeholder' => 'Выберите значение ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

        $variants = Html::tag('div', $select, $this->options);

        return $variants;
    }
}
