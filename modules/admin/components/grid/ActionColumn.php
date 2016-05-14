<?php

namespace app\modules\admin\components\grid;

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 22.11.2015
 * Project: salon-rukodeliya.loc
 * File name: ActionColumn.php
 */

use yii\bootstrap\Html;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $template = "<div class=\"btn-group w55\">{view} {update} {delete}</div>";

    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => \Yii::t('backend', 'Edit'),
                    'class' => 'btn btn-xs green',
                    'style' => 'margin-right:0',
                    'data-toggle' => 'tooltip',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="fa fa-edit"></span> ', $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => \Yii::t('backend', 'Delete'),
                    'class'=>'btn red btn-xs',
                    'data-toggle' => 'tooltip',
                    'data-confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="fa fa-trash-o"></span>', $url, $options);
            };
        }
    }
}
