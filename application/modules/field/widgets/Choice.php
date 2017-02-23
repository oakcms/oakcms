<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Yii;

class Choice extends \yii\base\Widgetr
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

        foreach($model->fields as $field) {
            $row = $this->renderField($field);
            $return[] = Html::tag('div', implode('', $row), ['class' => ' panel panel-default']);
        }

        if(empty($return)) {
            return null;
        }

        return Html::tag('div', implode('', $return), ['class' => 'oakcms-field']);
    }

    private function renderField($field)
    {
        $model = $this->model;

        $row = [];

        $row[] = Html::tag('div', Html::tag('strong', Html::a($field->name, ['/admin/field/field/update', 'id' => $field->id])), ['class' => 'panel-heading']);

        $variants = [];

        $options = [
            'class' => 'form-group option-variants field-data-container',
            'data-item-id' => $model->id,
            'data-id' => $field->id,
            'data-delete-action' => Url::toRoute(['/admin/field/field-value/delete']),
            'data-create-action' => Url::toRoute(['/admin/field/field-value/create']),
            'data-update-action' => Url::toRoute(['/admin/field/field-value/update']),
        ];

        switch($field->type) {
            case 'text':
                $variants[] = types\Text::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'date':
                $variants[] = types\Date::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'numeric':
                $variants[] = types\Numeric::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'textarea':
                $variants[] = types\TextArea::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'image':
                $variants[] = types\Image::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'radio':
                $variants[] = types\Radio::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'select':
                $variants[] = types\Select::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'checkbox':
                $variants[] = types\Checkbox::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            case 'textBlock':
                $variants[] = types\TextBlock::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                break;
            default:
                if(class_exists($field->type)) {
                    $class = $field->type;
                    $variants[] = $class::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                } else {
                    $variants[] = types\Select::widget(['field' => $field, 'model' => $this->model, 'options' => $options]);
                }
                break;
        }

        $row[] = Html::tag('div', Html::tag('div', implode('', $variants), ['class' => 'container-fluid']), ['class' => 'panel-body']);

        return $row;
    }
}
