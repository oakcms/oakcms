<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;

$this->title = 'Добавить склад';
$this->params['breadcrumbs'][] = ['label' => 'Склады', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="producer-create">
    <?= $this->render('_form', [
        'model' => $model,
        'activeStaffers' => $activeStaffers,
    ]) ?>
</div>
