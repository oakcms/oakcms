<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\form_builder\models\FormBuilderForms */

$this->title = Yii::t('form_builder', 'Update {modelClass}: ', [
    'modelClass' => 'Form Builder Forms',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('form_builder', 'Form Builder Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('form_builder', 'Update').': '.$model->title;
?>
<div class="form-builder-forms-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
