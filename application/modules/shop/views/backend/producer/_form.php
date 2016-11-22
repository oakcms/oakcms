<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\modules\gallery\widgets\Gallery;
use kartik\select2\Select2;
use app\modules\seo\widgets\SeoForm;

?>

<div class="producer-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'text')->widget(\app\widgets\Editor::className()) ?>

    <?=Gallery::widget(['model' => $model]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
