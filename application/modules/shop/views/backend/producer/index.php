<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;
use kartik\export\ExportMenu;

$this->title = 'Производители';
$this->params['breadcrumbs'][] = $this->title;

\app\modules\shop\assets\BackendAsset::register($this);
?>
<div class="producer-index">

    <div class="row">
        <div class="col-md-2">
            <?= Html::a('Создать производителя', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'filter' => false,
                'options' => ['style' => 'width: 55px;']
            ],

            'name',
            [
				'attribute' => 'image',
				'format' => 'image',
                'filter' => false,
				'content' => function ($image) {
                    if($image = $image->getImage()->getUrl('50x50')) {
                        return "<img src=\"{$image}\" class=\"thumb\" />";
                    }
				}
			],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttonOptions' => ['class' => 'btn btn-default'],
                'options' => ['style' => 'width: 125px;']
            ],
        ],
    ]); ?>

</div>
