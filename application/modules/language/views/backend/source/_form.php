<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\LanguageSource */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'),
        'options' => [
            'form' => 'language-source-id',
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
        'label' => Yii::t('admin', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#language-source-id")',
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

$modules = [];
foreach (Yii::$app->modules as $k => $module) {
    $modules += [$k=>$k];
}
foreach (Yii::$app->modules['admin']->modules as $k => $module) {
    $modules += [$k=>$k];
}

?>

<div class="language-source-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'language-source-id',
        ],
    ]); ?>

    <?= $form->field($model, 'category')->dropDownList($modules) ?>
    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
