<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Settings */
/* @var $form yii\widgets\ActiveForm */

if($model->isNewRecord) {
?>
    <div class="field-variant-form">

        <?php $form = ActiveForm::begin(['action' => ['/field/field-variant/create']]); ?>

        <?= $form->field($model, 'field_id')->hiddenInput()->label(false); ?>

        <div class="form-group field-field-name required">
            <textarea name="list" class="form-control" style="width: 400px; height: 160px;"></textarea>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Добавить список', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
}
?>
