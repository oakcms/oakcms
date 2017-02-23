<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Settings */
/* @var $form yii\widgets\ActiveForm */

if($model->isNewRecord) {
?>
    <div class="filter-variant-form-add">

        <?php $form = ActiveForm::begin(['action' => ['/admin/filter/filter-variant/create']]); ?>

        <?= $form->field($model, 'filter_id')->hiddenInput()->label(false); ?>

        <div class="form-group field-filter-name required">
            <textarea name="list" class="form-control" style="width: 400px; height: 160px;"></textarea>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Добавить список', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
} else {
?>
    <div class="filter-variant-form">

        <?php $form = ActiveForm::begin(['action' => ['/admin/filter/filter-variant/update', 'id' => $model->id], 'options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'filter_id')->hiddenInput()->label(false); ?>

        <?= $form->field($model, 'value')->textInput(); ?>

        <?=\app\modules\gallery\widgets\Gallery::widget(['model' => $model]); ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php } ?>

