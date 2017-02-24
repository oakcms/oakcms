<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentPages */

$this->title = Yii::t('content', 'Update {modelClass}: ', [
    'modelClass' => 'Content Pages',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'Content Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('content', 'Update').': '.$model->id;
?>
<div class="content-pages-update">

    <?= $this->render('_form', [
        'model' => $model,
        'lang' => $lang,
        'layouts' => $layouts,
    ]) ?>

</div>
