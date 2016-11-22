<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\seo\models\SeoItems */

$this->title = Yii::t('seo', 'Update {modelClass}: ', [
    'modelClass' => 'Seo Items',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('seo', 'Seo Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('seo', 'Update').': '.$model->title;
?>
<div class="seo-items-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
