<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\admin\components\grid;


use app\modules\admin\widgets\Html;

class DeleteColumn extends ActionColumn
{
    /*
     <a aria-disabled="false" tabindex="0" class="">

    </a>
    */

    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => \Yii::t('admin', 'Delete'),
                    'class' => 'md-icon-button',
                    'data-toggle' => 'tooltip',
                    'data-confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ], $this->buttonOptions);

                return Html::a('
                    <span class="md-button-wrapper">
                        <span class="fa fa-trash-o"></span>
                    </span>
                    <div class="md-button-ripple md-button-ripple-round">
                        <div class="md-ripple-background" style="background-color: rgba(0, 0, 0, 0);"></div>
                    </div>
                    <div class="md-button-focus-overlay"></div>
                ', $url, $options);
            };
        }
    }
}
