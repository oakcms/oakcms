<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;

$this->title = 'Создать производителя';
$this->params['breadcrumbs'][] = ['label' => 'Производители', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="producer-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
