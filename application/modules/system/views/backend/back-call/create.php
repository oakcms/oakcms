<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\system\models\SystemBackCall */

$this->title = Yii::t('system', 'Create System Back Call');
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'System Back Calls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-back-call-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
