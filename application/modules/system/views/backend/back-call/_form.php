<?php

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;
use app\modules\system\models\SystemBackCall;

/* @var $this yii\web\View */
/* @var $model app\modules\system\models\SystemBackCall */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('system', 'Create') : Yii::t('system', 'Update'),
        'options' => [
            'form' => 'system-back-call-id',
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
        'label' => Yii::t('system', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#system-back-call-id")',
        ],
        'icon' => 'fa fa-check-circle',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
];

function status($status) {
    switch ($status) {
        case 1:
            return '<span class="label label-primary">'.SystemBackCall::getStatus($status).'</span>';
            break;
        case 2:
            return '<span class="label label-success">'.SystemBackCall::getStatus($status).'</span>';
            break;
    }
}
?>

<div class="system-back-call-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'system-back-call-id',
        ],
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'comment')->textarea(['maxlength' => true]) ?>
    <?= $form->field($model, 'created_at')->staticField(date('d.m.Y H:i', $model->created_at)) ?>
    <?= $form->field($model, 'status')->staticField(status($model->status)) ?>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
