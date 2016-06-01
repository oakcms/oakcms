<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentArticles */

$this->title = Yii::t('admin', 'Update {modelClass}: ', [
    'modelClass' => 'Content Articles',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Content Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update').': '.$model->id;
?>
<div class="content-articles-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
