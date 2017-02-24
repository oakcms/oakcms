<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\widgets;

use app\modules\filter\models\Filter;
use yii\helpers\Html;
use yii\helpers\Url;

class Choice extends \yii\base\Widget
{
    public $model = null;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $return = [];
        $model = $this->model;

        foreach ($model->filters as $filter) {

            if($filter->is_filter == Filter::IS_FILTER) {
                $row = $this->renderFilter($filter);
                $return[] = Html::tag('div', implode('', $row), ['class' => ' box box-primary']);
            }
        }

        if (empty($return)) {
            return null;
        }

        return Html::tag('div', implode('', $return), ['class' => 'oakcms-filter']);
    }

    private function renderFilter($filter)
    {
        $row = [];

        $model = $this->model;

        $row[] = Html::tag('div', Html::tag('strong', "$filter->name ($filter->slug)"), ['class' => 'box-header with-border']);

        $variants = [];

        $options = [
            'class'              => 'form-group option-variants filter-data-container',
            'data-item-id'       => $model->id,
            'data-id'            => $filter->id,
            'data-delete-action' => Url::toRoute(['/admin/filter/filter-value/delete']),
            'data-create-action' => Url::toRoute(['/admin/filter/filter-value/create']),
            'data-update-action' => Url::toRoute(['/admin/filter/filter-value/update']),
        ];

        if ($filter->type == 'radio') {
            $variants[] = types\Select::widget(['filter' => $filter, 'model' => $this->model, 'options' => $options]);
        } else {
            $variants[] = types\Checkbox::widget(['filter' => $filter, 'model' => $this->model, 'options' => $options]);
        }

        $row[] = Html::tag('div', implode('', $variants), ['class' => 'box-body']);


        return $row;
    }
}
