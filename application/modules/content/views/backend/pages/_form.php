<?php

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;
use app\modules\language\models\Language;

$asset = \app\templates\backend\base\assets\BaseAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentPages */
/* @var $form yii\widgets\ActiveForm */
/* @var $layouts */

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
        'label' => $model->isNewRecord ? Yii::t('content', 'Create') : Yii::t('content', 'Update'),
        'options' => [
            'form' => 'content-pages-id',
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
        'label' => Yii::t('content', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#content-pages-id")',
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

<div class="content-pages-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'content-pages-id',
            'enctype' => 'multipart/form-data'
        ],
    ]); ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true])->translatable() ?>
    <?php echo $form->field($model, 'slug')
        ->hint(Yii::t('admin', 'If you\'ll leave this field empty, slug will be generated automatically'))
        ->textInput(['maxlength' => true])->translatable() ?>

    <?= $form->field($model, 'content')->widget(\app\widgets\Editor::className())->translatable() ?>
    <?= $form->field($model, 'description')->widget(\app\widgets\Editor::className())->translatable() ?>

    <div class="form-group">
        <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-9">
                <?= Html::img($model->getUploadUrl('background_image')?:'/uploads/user/non_image.png', ['class' => 'img-thumbnail', 'style'=>'max-width:300px']) ?>
                <br>
                <?= $model->getUploadUrl('background_image') ? Html::a(Yii::t('content', 'Delete Image'), ['delete-image', 'id' => $model->id], ['class' => 'label-danger']) : '' ?>
            </div>
        </div>
    </div>
    <?= $form->field($model, 'background_image')->fileInput(['accept' => 'image/*']) ?>

    <?= $form->field($model, 'layout')->dropDownList($layouts) ?>
    <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
    <?= $form->field($model, 'created_at')->staticField(date('d.m.Y H:i', $model->created_at)) ?>
    <?= $form->field($model, 'updated_at')->staticField(date('d.m.Y H:i', $model->updated_at)) ?>

    <?php echo $form->field($model, 'meta_title')->textInput(['maxlength' => true])->translatable() ?>
    <?php echo $form->field($model, 'meta_keywords')->textInput(['maxlength' => true])->translatable() ?>
    <?php echo $form->field($model, 'meta_description')->textInput(['maxlength' => true])->translatable() ?>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
