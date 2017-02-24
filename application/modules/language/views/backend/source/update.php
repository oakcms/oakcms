<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\LanguageSource */

$this->title = Yii::t('language', 'Update {modelClass}: ', [
    'modelClass' => 'Language Source',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Language Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('language', 'Update').': '.$model->id;
?>
<div class="language-source-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
