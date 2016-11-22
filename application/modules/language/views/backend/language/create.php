<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\language\models\Language */

$this->title = Yii::t('language', 'Create Language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
