<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\widgets\types;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use Yii;

class Select extends \yii\base\Widget
{
    public $model = NULL;
    public $filter = null;
    public $options = [];

    public function init()
    {
        \app\modules\filter\assets\VariantsAsset::register($this->getView());
        parent::init();
    }

    public function run()
    {
        $variantsList = $this->filter->variants;

        $variantsList = ArrayHelper::map($variantsList, 'id', 'value');
        $variantsList[0] = '-';
        ksort($variantsList);

        $checked = false;
        foreach($variantsList as $variantId => $value) {
            if($this->model->checkedId($variantId)) {
                $checked = $variantId;
                break;
            }
        }

        $select = Select2::widget([
            'name' => 'choise-option',
            'value' => $checked,
            'data' => $variantsList,
            'language' => 'ru',
            'options' => ['placeholder' => 'Выберите значение ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

        $variants = Html::tag('div', $select, $this->options);

        $new = [];
        $new[] = Html::input('text', 'variant_value', '', ['placeholder' => 'Новый вариант', 'data-filter-id' => $this->filter->id, 'data-create-action' => Url::toRoute(['/admin/filter/filter-variant/create']), 'class' => ' form-control']);
        $new[] = Html::button(Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['class' => 'btn btn-success']);

        $variants .= Html::tag('div', implode('', $new), ['class' => 'new-variant']);

        return $variants;
    }
}
