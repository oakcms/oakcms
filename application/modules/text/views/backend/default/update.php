<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\text\models\Text */

$this->title = Yii::t('carousel', 'Update {modelClass}: ', [
    'modelClass' => 'Text',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('carousel', 'Texts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('carousel', 'Update').': '.$model->title;
?>
<div class="text-update">

    <?= $this->render('_form', [
        'model' => $model,
        'lang'  => $lang,
        'layouts' => $layouts,
        'positions' => $positions
    ]) ?>

</div>
