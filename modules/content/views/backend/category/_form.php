<?php

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentCategory */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('content', 'Create') : Yii::t('content', 'Update'),
        'options' => [
            'form' => 'content-category-id',
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
        'label' => Yii::t('content', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#content-category-id")',
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

<div class="content-category-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'content-category-id',
        ],
    ]); ?>

    <?= $form->field($model, 'status')->textInput() ?>
    <?= $form->field($model, 'created_at')->textInput() ?>
    <?= $form->field($model, 'updated_at')->textInput() ?>
    <?= $form->field($model, 'root')->textInput() ?>
    <?= $form->field($model, 'lft')->textInput() ?>
    <?= $form->field($model, 'rgt')->textInput() ?>
    <?= $form->field($model, 'lvl')->textInput() ?>
    <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'icon_type')->textInput() ?>
    <?= $form->field($model, 'active')->textInput() ?>
    <?= $form->field($model, 'selected')->textInput() ?>
    <?= $form->field($model, 'disabled')->textInput() ?>
    <?= $form->field($model, 'readonly')->textInput() ?>
    <?= $form->field($model, 'visible')->textInput() ?>
    <?= $form->field($model, 'collapsed')->textInput() ?>
    <?= $form->field($model, 'movable_u')->textInput() ?>
    <?= $form->field($model, 'movable_d')->textInput() ?>
    <?= $form->field($model, 'movable_l')->textInput() ?>
    <?= $form->field($model, 'movable_r')->textInput() ?>
    <?= $form->field($model, 'removable')->textInput() ?>
    <?= $form->field($model, 'removable_all')->textInput() ?>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
