<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;

$this->title = 'Добавить модификацию';
\app\modules\shop\assets\ModificationConstructAsset::register($this);
?>
<div class="product-modification-create">

    <?= $this->render('_form', [
        'model' => $model,
        'productModel' => $productModel
    ]) ?>

</div>
