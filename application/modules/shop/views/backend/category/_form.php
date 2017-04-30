<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\shop\models\Category;
use app\modules\gallery\widgets\Gallery;
use kartik\select2\Select2;
use app\modules\seo\widgets\SeoForm;

?>

<div class="category-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'slug')->textInput(['placeholder' => 'Не обязательно']) ?>

	<?= $form->field($model, 'sort')->textInput() ?>

    <?php echo $form->field($model, 'text')->widget(\app\widgets\Editor::className()) ?>

    <?= $form->field($model, 'parent_id')->dropDownList(Category::buildTextTree(null, 1, [$model->id]), ['prompt' => Yii::t('shop', 'Select category')]); ?>

    <?//= Gallery::widget(['model' => $model]);?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
