<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\filter\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\filter\models\Filter;
use yii2mod\slider\IonSlider;
use yii;

class FilterPanel extends \yii\base\Widget
{
    public $itemId = null;
    public $filterId = null;
    public $itemCssClass = 'item';
    public $fieldName = 'filter';
    public $blockCssClass = 'block';
    public $findModel = false; //::find() модели, по которой будем искать соответствия
    public $ajaxLoad = false; //Ajax подгрузка результатов
    public $resultHtmlSelector = null; //CSS селектор, который хранит результаты
    public $submitButtonValue = 'Показать';

    public function init()
    {
        parent::init();

        if ($this->ajaxLoad) {
            \app\modules\filter\assets\FrontendAjaxAsset::register($this->getView());
        } else {
            \app\modules\filter\assets\FrontendAsset::register($this->getView());
        }
    }

    public function run()
    {
        $params = ['is_filter' => 'yes'];

        if ($this->filterId) {
            $params['id'] = $this->filterId;
        }

        $filters = Filter::find()->andWhere($params)->orderBy('sort DESC')->all();

        $return = [];
        $return[] = Html::beginTag('div', ['class' => 'filter_left_sidebar']);
        foreach ($filters as $filter) {

            if (in_array($this->itemId, $filter->selected)) {
                $block = '';
                $title = Html::tag('span', $filter->name, ['class' => 'filter_name']);

                if ($filter->description != '') {
                    $title .= Html::button('', ['class' => 'btn btn-default', 'title' => htmlspecialchars($filter->description), 'data' => ['toggle' => 'tooltip', 'placement' => 'right']]);
                }

                if ($this->findModel) {
                    $variants = $filter->getVariantsByFindModel($this->findModel)->all();
                } else {
                    $variants = $filter->variants;
                }

                if ($filter->type == 'range') {
                    $max = 0;
                    $min = 0;

                    foreach ($variants as $variant) {
                        if ($max < $variant->numeric_value) {
                            $max = $variant->numeric_value;
                        }
                        if ($min > $variant->numeric_value) {
                            $min = $variant->numeric_value;
                        }
                    }

//                    $fieldName = $this->fieldName . '[' . $filter->id . ']';
//
//                    $from = $min;
//                    $to = $max;
//
//                    $value = yii::$app->request->get($this->fieldName)[$filter->id];
//
//                    if ($value) {
//                        $values = explode(';', $value);
//                        $from = $values[0];
//                        $to = $values[1];
//                    }
//
//                    if (!empty($variants)) {
//                        $step = round($max / count($variants));
//                    } else {
//                        $step = 1;
//                    }

                    /*$block = IonSlider::widget([
                        'name'          => $fieldName,
                        'value'         => $value,
                        'type'          => "double",
                        'pluginOptions' => [
                            'drag_interval' => true,
                            'grid'          => true,
                            'min'           => $min,
                            'max'           => $max,
                            'from'          => $from,
                            'to'            => $to,
                            'step'          => $step,
                        ],
                    ]);*/
                } elseif ($filter->type == 'select') {
                    $fieldName = $this->fieldName . '[' . $filter->id . ']';

                    $value = yii::$app->request->get($this->fieldName)[$filter->id];

                    $variantsListWithNull = ['' => '-'];

                    $variantsList = ArrayHelper::map($variants, 'id', 'value');

                    foreach ($variantsList as $id => $item) {
                        $variantsListWithNull[$id] = $item;
                    }

                    $block = Html::dropDownList($fieldName, $value, $variantsListWithNull, ['class' => 'form-control']);
                } else {
                    foreach ($variants as $variant) {
                        $checked = false;
                        if ($filterData = yii::$app->request->get('filter')) {
                            if (isset($filterData[$filter->id]) && (isset($filterData[$filter->id][$variant->id]) | $filterData[$filter->id] == $variant->id)) {
                                $checked = true;
                            }
                        }

                        if (!in_array($filter->type, ['radio', 'checkbox', 'range'])) {
                            $filter->type = 'checkbox';
                        }

                        if ($filter->type == 'radio') {
                            $fieldName = $this->fieldName . '[' . $filter->id . ']';
                        } else {
                            $fieldName = $this->fieldName . '[' . $filter->id . '][' . $variant->id . ']';
                        }

                        $field = Html::label(Html::input($filter->type, $fieldName, $variant->id, ['checked' => $checked, 'data-item-css-class' => $this->itemCssClass]) . ' ' . $variant->value);

                        $block .= Html::tag('div', $field, ['class' => 'checkbox']);
                    }
                }

                if (!empty($variants)) {
                    $block .= '<div class="line "></div>';
                    $return[] = Html::tag('div', $title . $block, ['class' => $this->blockCssClass]);
                }
            }
        }

        if (!empty($return)) {
            $return[] = Html::input('submit', '', $this->submitButtonValue, ['class' => 'btn btn-submit']);
            $return[] = Html::endTag('div');

            return Html::tag('form', implode('', $return), ['data-resulthtmlselector' => $this->resultHtmlSelector, 'name' => 'oakcms-filter', 'action' => '', 'class' => 'oakcms-filter']);
        }

        return null;
    }
}
