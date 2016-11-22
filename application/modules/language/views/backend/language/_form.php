<?php

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\Language */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('language', 'Create') : Yii::t('language', 'Update'),
        'options' => [
            'form' => 'language-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ],
    [
        'label' => Yii::t('language', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#language-id")',
        ],
        'icon' => 'fa fa-check-circle',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
]
?>

<div class="language-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'language-id',
        ],
    ]); ?>

    <?= $form->field($model, 'language_id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'language')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name_ascii')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
