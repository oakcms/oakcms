<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\widgets;

use yii\helpers\Html;
use yii;

class Show extends \yii\base\Widget
{
    public $model = NULL;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $lis = [];

        if($fields = $this->model->fields) {
            foreach($fields as $field) {
                $value = $this->model->getField($field->slug);
                if(is_array($value)) {
                    $value = implode(', ', $value);
                }
                $lis[] = $field->name.': '.$value;
            }
        }

        return Html::ul($lis);
    }
}
