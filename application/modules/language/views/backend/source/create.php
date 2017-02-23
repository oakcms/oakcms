<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\language\models\LanguageSource */

$this->title = Yii::t('language', 'Create Language Source');
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Language Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-source-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
