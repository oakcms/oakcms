<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\filter\widgets;

use app\modules\filter\models\Filter;
use app\modules\filter\models\FilterVariants;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Yii;

class Choice extends \yii\base\Widget
{
    public $model = NULL;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $return = [];
        $model = $this->model;

        foreach($model->filters as $filter) {
            $row = $this->renderFilter($filter);
            $return[] = Html::tag('div', implode('', $row), ['class' => ' panel panel-default']);
        }

        if(empty($return)) {
            return null;
        }

        return Html::tag('div', implode('', $return), ['class' => 'pistol88-filter']);
    }

    private function renderFilter($filter)
    {
        $model = $this->model;

        $row = [];

        $row[] = Html::tag('div', Html::tag('strong', "$filter->name ($filter->slug)"), ['class' => 'panel-heading']);

        $variants = [];

        $options = [
            'class' => 'form-group option-variants filter-data-container',
            'data-item-id' => $model->id,
            'data-id' => $filter->id,
            'data-delete-action' => Url::toRoute(['/filter/filter-value/delete']),
            'data-create-action' => Url::toRoute(['/filter/filter-value/create']),
            'data-update-action' => Url::toRoute(['/filter/filter-value/update']),
        ];

        if($filter->type == 'radio') {
            $variants[] = types\Select::widget(['filter' => $filter, 'model' => $this->model, 'options' => $options]);
        } else {
            $variants[] = types\Checkbox::widget(['filter' => $filter, 'model' => $this->model, 'options' => $options]);
        }

        $row[] = Html::tag('div', implode('', $variants), ['class' => 'panel-body']);

        return $row;
    }
}
