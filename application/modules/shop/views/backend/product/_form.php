<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */
/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentArticles */
/* @var $form ActiveForm */
/* @var $lang Language */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\modules\shop\models\Category;
use app\modules\shop\models\Producer;
use app\modules\gallery\widgets\Gallery;
use kartik\select2\Select2;
use app\modules\admin\widgets\Button;
use app\modules\language\models\Language;

$asset = \app\templates\backend\base\assets\BaseAsset::register($this);
\app\modules\shop\assets\BackendAsset::register($this);

// Language
if($model->isNewRecord) {
    $langueBtn = [
        'label' => '<img src="'.$asset->baseUrl.'/images/flags/'.$lang->url.'.png" alt="'.$lang->url.'"/> '.Yii::t('backend', 'Language'),
        'options' => [
            'form' => 'portfolio-id',
            'type' => 'submit',
        ],
        'encodeLabel' => false,
        'icon' => false,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-default'
    ];
} else {
    $allLang = Language::getLanguages();
    $langueBtnItems = [];
    foreach($allLang as $item) {
        if($lang->language_id != $item->language_id) {
            $langueBtnItems[] = [
                'label' => '<img src="'.$asset->baseUrl.'/images/flags/'.$item->url.'.png" alt="'.$item->url.'"/> '.$item->name,
                'url' => ['update', 'id' => $model->id, 'language' => $item->url]
            ];
        }
    }


    $langueBtn = [
        'label' => '<img src="'.$asset->baseUrl.'/images/flags/'.$lang->url.'.png" alt="'.$lang->url.'"/> '.$lang->name,
        'options' => [
            'class' => 'btn blue btn-outline btn-circle btn-sm',
            'data-hover'=>"dropdown",
            'data-close-others'=>"true",
        ],
        'encodeLabel' => false,
        'dropdown' => [
            'encodeLabels' => false,
            'options' => ['class' => 'pull-right'],
            'items' => $langueBtnItems,
        ],
    ];
}

$this->params['actions_buttons'] = [
    $langueBtn,
    [
        'label' => $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'),
        'options' => [
            'form' => 'content-articles-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ],
    [
        'label' => Yii::t('admin', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#content-articles-id")',
        ],
        'icon' => 'fa fa-check-circle',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
]

?>

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>
        <div class="col-lg-6 col-xs-6">
            <?= $form->field($model, 'slug')->textInput(['placeholder' => 'Не обязательно']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <?= $form->field($model, 'amount')->textInput() ?>
        </div>
        <div class="col-lg-6 col-xs-6">
            <?= $form->field($model, 'code')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-xs-2">
            <?php if($model->isNewRecord) $model->available = 'yes'; ?>
            <?= $form->field($model, 'available')->radioList(['yes' => 'Да','no' => 'Нет']); ?>
        </div>
        <div class="col-lg-2 col-xs-2">
            <?php if($model->isNewRecord) $model->is_new = 'no'; ?>
            <?= $form->field($model, 'is_new')->radioList(['yes' => 'Да','no' => 'Нет']); ?>
        </div>
        <div class="col-lg-2 col-xs-2">
            <?php if($model->isNewRecord) $model->is_popular = 'no'; ?>
            <?= $form->field($model, 'is_popular')->radioList(['yes' => 'Да','no' => 'Нет']); ?>
        </div>
        <div class="col-lg-2 col-xs-2">
            <?php if($model->isNewRecord) $model->is_promo = 'no'; ?>
            <?= $form->field($model, 'is_promo')->radioList(['yes' => 'Да','no' => 'Нет']); ?>
        </div>
        <div class="col-lg-2 col-xs-2">
            <?= $form->field($model, 'sort')->textInput() ?>
        </div>
    </div>



    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <?= $form->field($model, 'category_id')
                ->widget(Select2::classname(), [
                    'data' => Category::buildTextTree(),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите категорию ...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>

            <?= $form->field($model, 'producer_id')
                ->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Producer::find()->all(), 'id', 'name'),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите бренд ...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
        </div>
        <div class="col-lg-6 col-xs-6">
            <?= $form->field($model, 'category_ids')
                ->label('Прочие категории')
                ->widget(Select2::classname(), [
                    'data' => Category::buildTextTree(),
                    'language' => 'ru',
                    'options' => ['multiple' => true, 'placeholder' => 'Доп. категории ...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
        </div>
    </div>

    <?php echo $form->field($model, 'text')->widget(\app\widgets\Editor::className()) ?>

    <?= $form->field($model, 'short_text')->textInput(['maxlength' => true]) ?>

    <?=Gallery::widget(['model' => $model]); ?>

    <div class=" panel panel-default">
        <div class="panel-heading"><strong>Связанные продукты</strong></div>
        <div class="panel-body">
            <?= \app\modules\relations\widgets\Constructor::widget(['model' => $model]);?>
        </div>
    </div>

    <?php if(isset($priceTypes)) { ?>
        <?php if($priceTypes) { ?>
            <h3>Цены</h3>
            <?php $i = 1; foreach($priceTypes as $priceType) { ?>
                <?= $form->field($priceModel, "[{$priceType->id}]price")->label($priceType->name); ?>
                <?php $i++; } ?>
        <?php } ?>
    <?php } ?>

    <div class="form-group shop-control">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php if(!$model->isNewRecord) { ?>
            <a class="btn btn-default" href="<?=Url::toRoute(['product/delete', 'id' => $model->id]);?>" title="Удалить" aria-label="Удалить" data-confirm="Вы уверены, что хотите удалить этот элемент?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>
        <?php } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
