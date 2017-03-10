<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\form_builder\models\FormBuilderForms */

$this->title = Yii::t('form_builder', 'Create Form Builder Forms');
$this->params['breadcrumbs'][] = ['label' => Yii::t('form_builder', 'Form Builder Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-builder-forms-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
