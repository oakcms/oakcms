<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="filter-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="relationModels">
                <?= $form->field($model, 'relation_field_value')->checkboxList(Yii::$app->getModule('filter')->relationFieldValues) ?>
            </div>

            <?= $form->field($model, 'relation_field_name')->hiddenInput(['value' => Yii::$app->getModule('filter')->relationFieldName])->label(false); ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'slug')->textInput() ?>
            <?= $form->field($model, 'sort')->textInput() ?>
            <?= $form->field($model, 'is_filter')->dropdownList(['no' => 'Нет', 'yes' => 'Да']) ?>
            <?= $form->field($model, 'type')->dropdownList(Yii::$app->getModule('filter')->types) ?>
            <?= $form->field($model, 'description')->textArea(['maxlength' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style>
.relationModels label {
    display: block;
}
</style>
