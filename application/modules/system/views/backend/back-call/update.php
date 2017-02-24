<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\system\models\SystemBackCall */

$this->title = Yii::t('system', 'Update {modelClass}: ', [
    'modelClass' => 'System Back Call',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'System Back Calls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('system', 'Update').': '.$model->name;
?>
<div class="system-back-call-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
