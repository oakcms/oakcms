<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\export\ExportMenu;

$this->title = 'Типы цен';
$this->params['breadcrumbs'][] = $this->title;

\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="price-type-index">

    <div class="row">
        <div class="col-md-2">
            <?= Html::a('Добавить новый тип', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 55px;']],

            'name',
            'sort',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 125px;']],
        ],
    ]); ?>

</div>
