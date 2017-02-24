<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\Language */

$this->title = Yii::t('language', 'Update {modelClass}: ', [
    'modelClass' => 'Language',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('language', 'Update').': '.$model->name;
?>
<div class="language-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
