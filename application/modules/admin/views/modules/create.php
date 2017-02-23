<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\ModulesModules */

$this->title = Yii::t('admin', 'Create Modules Modules');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Modules Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modules-modules-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
