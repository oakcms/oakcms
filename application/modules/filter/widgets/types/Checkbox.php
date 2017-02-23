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
use Yii;

class Checkbox extends \yii\base\Widget
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

        $this->options['class'] .= ' filter-variants';
        $this->options['item'] = function($item, $index) {
            return $this->variant($item);
        };

        $variants = Html::ul($variantsList, $this->options);

        $new = [];
        $new[] = Html::input('text', 'variant_value', '', ['placeholder' => 'Новый вариант', 'data-filter-id' => $this->filter->id, 'data-create-action' => Url::toRoute(['/admin/filter/filter-variant/create']), 'class' => ' form-control']);
        $new[] = Html::button(Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['class' => 'btn btn-success']);

        $variants .= Html::tag('div', implode('', $new), ['class' => 'new-variant']);

        return $variants;
    }

    private function variant($item)
    {
        $return = [];

        $checked = $this->model->checkedId($item->id);

        $return[] = Html::checkbox('variant', $checked, ['id' => 'filtervariant'.$item->id, 'data-id' => $item->id]);
        $return[] = ' ';
        $return[] = Html::label($item->value, 'filtervariant'.$item->id);
        return Html::tag('li', implode('', $return));
    }
}
