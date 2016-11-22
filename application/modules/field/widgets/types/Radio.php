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

class Radio extends \yii\base\Widget
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

        $radio = Html::radioList('choice-field-value', $checked, $variantsList);

        $variants = Html::tag('div', $radio, $this->options);

        return $variants;
    }
}
