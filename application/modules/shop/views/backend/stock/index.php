<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;
use kartik\export\ExportMenu;

$this->title = 'Склады';
$this->params['breadcrumbs'][] = $this->title;

\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="producer-index">

    <div class="row">
        <div class="col-md-2">
            <?= Html::a('Добавить склад', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-md-4">
            <?php
            $gridColumns = [
                'id',
                'name',
            ];
            ?>
        </div>
    </div>

    <br style="clear: both;"></div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 55px;']],

            'name',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 125px;']],
        ],
    ]); ?>

</div>
