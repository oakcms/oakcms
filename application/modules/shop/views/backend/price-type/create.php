<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;

$this->title = 'Создать тип цен';
$this->params['breadcrumbs'][] = ['label' => 'Типы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="price-type-create">
    <div class="shop-menu">
        <?=$this->render('../parts/menu');?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
