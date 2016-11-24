<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;

$this->title = 'Добавить товар';
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="product-create">
    <div class="shop-menu">
        <?=$this->render('../parts/menu');?>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'priceTypes' => $priceTypes,
        'priceModel' => $priceModel,
        'lang' => $lang,
    ]) ?>

</div>
