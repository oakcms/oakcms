<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\admin\widgets;

use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use yii\jui\Spinner;

class ActiveField extends \yii\widgets\ActiveField {

    public $form;
    public $tagOptions = ['class' => 'col-md-9'];

    const ICON_POSITION_LEFT = 'left';
    const ICON_POSITION_RIGHT = 'right';

    public function end() {
        return Html::endTag(isset($this->options['tag']) ? $this->options['tag'] : 'div') . "\n";
    }

    public function translatable($position = null) {

        if(!$position) {
            $position = self::ICON_POSITION_RIGHT;
        }
        $this->parts['{input}'] = Html::tag('i', '', [
                'class' => 'fa fa-globe font-blue',
                'title' => \Yii::t('admin', 'Translatable element'),
                'data-toggle' => 'tooltip'
            ]) . "\n" . $this->parts['{input}'];
        $this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'input-icon ' . $position]);


        return $this;
    }

    public function staticField($value = false) {
        if(!$value) {
            $value = $this->model->{$this->attribute};
        }
        $this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute) . '<div class="form-control-static">' . $value . '</div>';
        return $this;
    }

    public function fileField($options = [])
    {

        if ($this->inputOptions !== ['class' => 'form-control']) {
            $options = array_merge($this->inputOptions, $options);
        }

        $icon = ArrayHelper::remove($options, 'icon', null);

        if (!$icon) {
            $icon = 'fa fa-bookmark-o';
        }
        $addon = Html::tag(
            'span',
            Html::tag('button',
                Html::tag(
                    'i',
                    '',
                    ['class' => $icon]
                ),
                ['class' => 'btn btn-default add-file', 'type' => 'button']
            ),
            ['class' => 'input-group-btn']
        );

        $options = array_merge($this->inputOptions, $options);
        $position = ArrayHelper::remove($options, 'position', self::ICON_POSITION_RIGHT);
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);

        if ($position == self::ICON_POSITION_LEFT) {
            $this->parts['{input}'] = $addon . "\n" . $this->parts['{input}'];
        } else {
            $this->parts['{input}'] .= "\n" . $addon;
        }
        $this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'input-group']);


        return $this;
    }

    /**
     * Generates a icon for input.
     * @param array $options icon options.
     * The options have following structure:
     * ```php
     * [
     *     'icon' => 'fa fa-bookmark-o',
     *     'position' => ActiveField::ICON_POSITION_LEFT,
     * ]
     * ```
     * @return static the field object itself
     */
    public function icon($options = []) {
        $icon = ArrayHelper::remove($options, 'icon', null);
        if ($icon) {
            $position = ArrayHelper::remove($options, 'position', self::ICON_POSITION_LEFT);
            if ($position != self::ICON_POSITION_RIGHT) {
                $position = '';
            }
            $this->parts['{input}'] = Html::tag('i', '', ['class' => $icon]) . "\n" . $this->parts['{input}'];
            $this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'input-icon ' . $position]);
        }

        return $this;
    }

    /**
     * Generates a groupAddon for input.
     * GroupAddon similar to [[icon()]].
     * @param array $options icon options.
     * The options have following structure:
     * ```php
     * [
     *     'icon' => 'fa fa-bookmark-o',
     *     'position' => ActiveField::ICON_POSITION_LEFT,
     * ]
     * ```
     * @return static the field object itself
     */
    public function groupAddon($options = [], $textOptions = []) {

        $text = ArrayHelper::remove($options, 'text', null);

        $options = array_merge($options, $this->inputOptions);

        if ($text) {
            $addon = Html::tag('span', Html::tag('span', $text, $textOptions), ['class' => 'input-group-addon']);
            $position = ArrayHelper::remove($options, 'position', self::ICON_POSITION_LEFT);

            $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);

            if ($position == self::ICON_POSITION_RIGHT) {
                $this->parts['{input}'] .= "\n" . $addon;
            } else {
                $this->parts['{input}'] = $addon . "\n" . $this->parts['{input}'];
            }
            $this->parts['{input}'] = Html::tag('div', $this->parts['{input}'], ['class' => 'input-group']);
        }

        return $this;
    }

    public function staticError($error, $options = []) {
        $options = array_merge($this->errorOptions, $options);
        $tag = isset($options['tag']) ? $options['tag'] : 'div';
        unset($options['tag']);
        $this->parts['{error}'] = Html::tag($tag, $error, $options);

        return $this;
    }

    public function spinner($options = []) {
        $this->parts['{input}'] = Spinner::widget(array_merge($options, ['model' => $this->model, 'attribute' => $this->attribute]));

        return $this;
    }

    public function dateRangePicker($options = []) {
        if ($this->form->type == ActiveForm::TYPE_VERTICAL) {
            //$options = array_merge($options, ['options' => ['style' => 'display:table-cell;']]);
            $options = array_merge($options, ['options' => ['class' => 'show']]);
        }
        $this->parts['{input}'] = DateRangePicker::widget(array_merge($options, ['model' => $this->model, 'attribute' => $this->attribute]));

        return $this;
    }

    public function datePicker($options = []) {
        /* if ($this->form->type == ActiveForm::TYPE_VERTICAL) {
          //$options = array_merge($options, ['options' => ['style' => 'display:table-cell;']]);
          $options = array_merge($options, ['options' => ['class' => 'show']]);
          } */
        $this->parts['{input}'] = DatePicker::widget(array_merge($options, ['model' => $this->model, 'attribute' => $this->attribute]));

        return $this;
    }

    public function select2($options = []) {
        $this->parts['{input}'] = Select2::widget(array_merge($options, ['model' => $this->model, 'attribute' => $this->attribute]));

        return $this;
    }
}
