<?php

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\seo\models\SeoItems */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'),
        'options' => [
            'form' => 'seo-items-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'green-jungle'
    ],
    [
        'label' => Yii::t('admin', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#seo-items-id")',
        ],
        'icon' => 'fa fa-check-circle',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'green-jungle'
    ]
];
?>

<div class="seo-items-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'seo-items-id',
        ],
    ]); ?>

    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'keywords')->textarea(['rows' => 3, 'style' => 'resize:none']) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'style' => 'resize:none']); //widget(\app\widgets\Editor::className()); ?>
    <?= $form->field($model, 'canonical')->textInput(['maxlength' => true]) ?>
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
