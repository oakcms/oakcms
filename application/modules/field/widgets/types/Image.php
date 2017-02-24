<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\widgets\types;

use app\modules\admin\widgets\InputFile;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Yii;

class Image extends \yii\base\Widget
{
    public $model = NULL;
    public $field = null;
    public $options = [];

    public function init()
    {
        \app\modules\field\assets\ValueAsset::register($this->getView());
        parent::init();
    }

    public function run()
    {
        $variantsList = $this->field->variants;

        $variantsList = ArrayHelper::map($variantsList, 'id', 'value');
        $variantsList[0] = '-';
        ksort($variantsList);

        $value = $this->model->getField($this->field->slug);

        $button = Html::button('<i class="glyphicon glyphicon-ok"></i>', ['class' => ' btn btn-success field-save-value']);

        $input = InputFile::widget([
            'id' => 'wid'.uniqid(),
            'language'   => \Yii::$app->language,
            //'filter'     => 'image',
            'name'       => 'choice-field-value',
            'value'      => $value,
            'template'   => '<div class="input-group">{input}<div class="input-group-btn">{button}'.$button.'</div></div>',
            'options'    => [
                'data-id' => $this->field->id,
                'data-item-id' => $this->model->id,
                'class' => 'form-control',
                'placeholder' => ''
            ]
        ]);
        $block = Html::tag('div', $input, $this->options);

        return $block;
    }
}
