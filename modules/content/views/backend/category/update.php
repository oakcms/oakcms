<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentCategory */

$this->title = Yii::t('content', 'Update {modelClass}: ', [
    'modelClass' => 'Content Category',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'Content Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('content', 'Update').': '.$model->id;
?>
<div class="content-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
