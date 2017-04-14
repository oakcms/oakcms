<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\search\ModulesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="modules-modules-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'module_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'class') ?>

    <?= $form->field($model, 'isFrontend') ?>

    <?= $form->field($model, 'controllerNamespace') ?>

    <?php // echo $form->field($model, 'viewPath') ?>

    <?php // echo $form->field($model, 'isAdmin') ?>

    <?php // echo $form->field($model, 'BackendControllerNamespace') ?>

    <?php // echo $form->field($model, 'AdminViewPath') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'icon') ?>

    <?php // echo $form->field($model, 'settings') ?>

    <?php // echo $form->field($model, 'order') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('admin', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('admin', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
