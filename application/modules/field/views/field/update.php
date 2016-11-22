<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

$this->title = 'Обновление поля';
$this->params['breadcrumbs'][] = ['label' => 'Поля', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновление';
?>
<div class="field-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php if(in_array($model->type, ['select', 'checkbox', 'radio'])) { ?>

        <hr />

        <h2>Варианты</h2>

        <div class="variants">
            <div class="row">
                <div class="col-lg-6">
                    <?= $this->render('_form_variant', [
                        'model' => $variantModel,
                    ]) ?>
                </div>
                <div class="col-lg-6">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            //['class' => 'yii\grid\SerialColumn'],
                            [
                                'class' => \dosamigos\grid\EditableColumn::className(),
                                'attribute' => 'value',
                                'filter' => false,
                                'url' => ['edit-variant'],
                                'editableOptions' => [
                                    'mode' => 'inline',
                                ],
                            ],
                            ['class' => 'yii\grid\ActionColumn',
                            'template' => '{delete}',
                            'buttons' => [
                                'delete' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/field/field-variant/delete', 'id' => $model->id], [
                                            'title' =>'Удалить',
                                            'data-method' => 'POST',
                                            'data-pjax'=>'1'
                                        ]);
                                    },
                            ],
                            'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 90px;']],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    
    <?php } ?>
</div>
