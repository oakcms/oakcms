<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\language\models\LanguageTranslate */

$this->title = Yii::t('language', 'Update {modelClass}: ', [
    'modelClass' => 'Language Translate',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Language Translates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('language', 'Update').': '.$model->id;
?>
<div class="language-translate-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
