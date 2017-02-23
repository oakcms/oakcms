<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\modules\field\models\Category;
?>

<div class="field-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'category_id')->dropdownList(ArrayHelper::map(Category::find()->all(), 'id', 'name')) ?>
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'slug')->textInput() ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'relation_model')->dropdownList(yii::$app->getModule('field')->relationModels) ?>
            <?php
            $relation_model = $model->relation_model;
            ?>
            <?if(!empty($relation_model) && method_exists($relation_model, 'getCategoryFields')):?>
                <?= $form->field($model, 'model_category_id')->checkboxList($relation_model::getCategoryFields()) ?>
            <?endif;?>
            <?= $form->field($model, 'type')->dropdownList(Yii::$app->getModule('field')->types) ?>
            <?= $form->field($model, 'description')->textArea(['maxlength' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
