<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\widgets\types;


use app\modules\text\models\Text;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class TextBlock extends \yii\base\Widget
{
    public $model = NULL;
    public $field = null;
    public $options = [];

    public function init()
    {
        \app\modules\field\assets\VariantsTextBlockAsset::register($this->getView());
        parent::init();
    }

    public function run()
    {
        $value = $this->model->getField($this->field->slug);

        $textBlocks = Text::find()->andWhere(['status' => Text::STATUS_PUBLISHED])->all();
        $variantsList = ArrayHelper::map($textBlocks, 'id', 'title');
        $select = Select2::widget([
            'name' => 'choice-field-value',
            'value' => $value,
            'data' => $variantsList,
            'language' => 'ru',
            'options' => ['placeholder' => 'Выберите значение ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

        $variants = Html::tag('div', $select, $this->options);

        return $variants;
    }
}
