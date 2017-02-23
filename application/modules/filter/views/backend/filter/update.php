<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Settings */

$this->title = 'Обновление фильтра';
$this->params['breadcrumbs'][] = ['label' => 'Фильтры', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновление';
?>
<div class="filter-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

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
                        [
                            'attribute' => 'image',
                            'filter' => false,
                            'content' => function($model) {
                                $form = '<div class="modal-dialog"><div class="modal-content"><div class="modal-body">'.$this->render('_form_variant', ['model' => $model]).'</div></div></div>';

                                if($model->hasImage()) {
                                    $anchor = Html::img($model->getImage()->getUrl('x50'), ['width' => '50']);
                                } else {
                                    $anchor = 'Загрузить картинку';
                                }

                                $link = Html::a($anchor, "#variantForm{$model->id}", ['data-toggle' => 'modal', 'data-target' => "#variantForm{$model->id}"]);

                                $window = Html::tag(
                                    'div',
                                    $form,
                                    [
                                        'class' => 'modal fade',
                                        'id' => "variantForm{$model->id}",
                                        'role' => 'dialog'
                                    ]
                                );

                                return $link.$window;
                            }
                        ],
                        ['class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/admin/filter/filter-variant/delete', 'id' => $model->id], [
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
</div>

