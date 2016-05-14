<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\seo\models\search\SeoItemsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="seo-items-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'link') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'keywords') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'canonical') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('seo', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('seo', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
