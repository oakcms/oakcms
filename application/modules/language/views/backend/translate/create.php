<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\language\models\LanguageTranslate */

$this->title = Yii::t('language', 'Create Language Translate');
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Language Translates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-translate-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
