<?php

namespace app\modules\admin\components\grid;

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 22.11.2015
 * Project: OakCMS
 * File name: ActionColumn.php
 */

use yii\bootstrap\Html;
use yii\helpers\Url;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $template = "<div class=\"btn-group w55\">{view} {update} {delete}</div>";
    public $translatable = false;

    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            if($this->translatable === true) {
                var_dump(\Yii::$app->session->get('_languages')['url']);
                $params['language'] = \Yii::$app->session->get('_languages')['url'];
            }
            return Url::toRoute($params);
        }
    }

    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => \Yii::t('admin', 'Edit'),
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
                    'title' => \Yii::t('admin', 'Delete'),
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
