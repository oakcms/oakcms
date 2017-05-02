<?php
use dosamigos\grid\EditableColumn;
use app\modules\admin\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="row">
    <div class="box-header with-border">
        <h3 class="box-title">
            <span style="color: #f0615c;font-size: 24px"><?= Yii::t('shop', 'Prices') ?></span>
        </h3>

        <div class="actions pull-right">
            <a href="#" class="btn btn-sm btn-success" onclick="$('.product-add-price-form').toggle(); return false;">Добавить <span class="glyphicon glyphicon-plus add-price"></span></a>
        </div>
    </div>
    <div class="box-body">
        <div class="product-add-price-form" style="<?php if(!$model->hasErrors()):?>display: none;<?php endif; ?>">
            <?php $form = ActiveForm::begin([]); ?>
            <?= $form->field($model, 'modification_id')->textInput(['type' => 'hidden', 'value' => $modification_id])->label(false) ?>
            <?= $form->field($model, 'name')->textInput(['value' => $model->name ? $model->name : Yii::t('shop', 'Main Price')]) ?>
            <?= $form->field($model, 'type_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(
                    \app\modules\shop\models\PriceType::find()->orderBy(['name' => SORT_ASC])->all(),
                    'id',
                    'name'
                )
            ) ?>
            <?= $form->field($model, 'price')->textInput() ?>
            <?php $model->available = 'yes'; ?>
            <?= $form->field($model, 'available')->radioList(['yes' => 'Да', 'no' => 'Нет']); ?>
            <div class="container-fluid">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

        <?php
        echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
            'filterModel'  => $searchModel,
            'columns'      => [
                [
                    'attribute' => 'id',
                    'filter' => false,
                    'options' => ['style' => 'width: 25px;']
                ],
                [
                    'attribute'       => 'type_id',
                    'value'           => function($model) {
                        return $model->type->name;
                    },
                    'options'         => ['style' => 'width: 75px;'],
                ],
                [
                    'class'           => EditableColumn::className(),
                    'attribute'       => 'available',
                    'url'             => ['price/edit-field'],
                    'type'            => 'select',
                    'editableOptions' => [
                        'mode'   => 'inline',
                        'source' => ['yes', 'no'],
                    ],
                    'filter'          => false,
                    'contentOptions'  => ['style' => 'width: 27px;'],
                ],
                [
                    'class'           => EditableColumn::className(),
                    'attribute'       => 'price',
                    'url'             => ['price/edit-field'],
                    'type'            => 'text',
                    'editableOptions' => [
                        'mode' => 'inline',
                    ],
                    'options'         => ['style' => 'width: 40px;'],
                ],
                ['class' => 'yii\grid\ActionColumn', 'controller' => 'price', 'template' => '{delete}', 'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 30px;']],
            ],
        ]); ?>
    </div>
</div>
