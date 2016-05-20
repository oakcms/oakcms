<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\ModulesModules */

$this->title = Yii::t('admin', 'Update {modelClass}: ', [
    'modelClass' => 'Modules Modules',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Modules Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update').': '.$model->name;
?>
<div class="modules-modules-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
