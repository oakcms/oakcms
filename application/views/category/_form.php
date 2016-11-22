<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\admin\widgets\Button;
use app\modules\language\models\Language;
use app\templates\backend\base\assets\BaseAsset;
use app\modules\content\models\ContentCategory;

$asset = BaseAsset::register($this);
$class = $this->context->categoryClass;
$settings = $this->context->module->settings;

if(isset($parent))
    $model->parent = $parent;

if($model->isNewRecord) {
    $langueBtn = [
        'label' => '<img src="'.$asset->baseUrl.'/images/flags/'.$lang->url.'.png" alt="'.$lang->url.'"/> '.Yii::t('backend', $lang->name),
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
}
else {
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
            'form' => 'category-form-id',
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
            'onclick' => 'sendFormReload("#category-form-id")',
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
<?php $form = \app\modules\admin\widgets\ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => [
        'enctype' => 'multipart/form-data',
        'id'=>'category-form-id'
    ]
]); ?>

    <?= $form->field($model, 'title') ?>
    <?= $form->field($model, 'slug')->hint(Yii::t('admin', 'If you\'ll leave this field empty, slug will be generated automatically')) ?>
    <?= (isset($parent)) ? $form->field($model, 'parent')->dropDownList(ArrayHelper::map(
        ContentCategory::find()->andFilterWhere(['<>', 'id', $model->id])->all(), 'id', 'title'
    ), ['prompt' => '']) : '' ?>

    <?php if($settings['categoryThumb']['value']) : ?>

    <?php endif; ?>

    <?= $form->field($model, 'meta_title')->textInput() ?>
    <?= $form->field($model, 'meta_keywords')->textInput() ?>
    <?= $form->field($model, 'meta_description')->textInput() ?>
<?php \app\modules\admin\widgets\ActiveForm::end(); ?>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
