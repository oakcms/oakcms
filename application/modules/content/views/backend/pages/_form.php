<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;
use app\modules\language\models\Language;

$asset = \app\templates\backend\base\assets\BaseAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentPages */
/* @var $form yii\widgets\ActiveForm */
/* @var $lang Language */
/* @var $layouts */

if($model->isNewRecord) {
    $langueBtn = [
        'label'       => '<img src="' . $asset->baseUrl . '/images/flags/' . $lang->url . '.png" alt="' . $lang->url . '"/> ' . Yii::t('admin', 'Language'),
        'options'     => [
            'form' => 'portfolio-id',
            'type' => 'submit',
        ],
        'encodeLabel' => false,
        'icon'        => false,
        'size'        => Button::SIZE_SMALL,
        'disabled'    => false,
        'block'       => false,
        'type'        => Button::TYPE_CIRCLE,
        'color'       => 'btn-default',
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
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab_1" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-file-text-o"></i> <?= Yii::t('content', 'Content') ?>
                </a>
            </li>
            <li class="">
                <a href="#tab_2" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-newspaper-o"></i> <?= Yii::t('content', 'Publication') ?>
                </a>
            </li>
            <li class="">
                <a href="#seoTab" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-star"></i> <?= Yii::t('content', 'SEO') ?>
                </a>
            </li>
            <li class="">
                <a href="#fields" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-plus-square"></i> <?= Yii::t('content', 'Relation Fields') ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <?php echo $form->field($model, 'title')->textInput(['maxlength' => true])->translatable() ?>
            <?php echo $form->field($model, 'slug')
                ->hint(Yii::t('admin', 'If you\'ll leave this field empty, slug will be generated automatically'))
                ->textInput(['maxlength' => true])->translatable() ?>

            <?php
            $parents = \app\modules\content\models\ContentPages::find()->excludeRoots();
            if(!$model->isNewRecord) {
                $parents->andWhere(['<>', 'id', $model->id]);
            }
            ?>

            <?php echo $form->field($model, 'parent_id')->widget(\kartik\select2\Select2::className(), [
                'initValueText' => $model->parent ? ($model->parent->isRoot() ? Yii::t('content', 'Top Level') : $model->parent->title) : null,
                'theme'         => \kartik\select2\Select2::THEME_BOOTSTRAP,
                'data'          => \yii\helpers\ArrayHelper::map($parents->orderBy('lft')->all(), 'id', function (
                    $model
                ) {
                    /** @var $model \app\modules\menu\models\MenuItem */
                    return str_repeat("- ", max($model->level - 1, 0)) .
                        $model->title;
                }),
                'pluginOptions' => [
                    'allowClear'  => true,
                    'placeholder' => Yii::t('content', 'Top Level'),
                ],
            ]) ?>

            <?= $form->field($model, 'content')->widget(\app\widgets\Editor::className())->translatable() ?>
            <?= $form->field($model, 'description')->widget(\app\widgets\Editor::className())->translatable() ?>
            <?= $form->field($model, 'background_image')->widget(\app\modules\admin\widgets\InputFile::className()) ?>

            <?= $form->field($model, 'layout')->dropDownList($layouts) ?>
        </div>
        <div class="tab-pane" id="tab_2">
            <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
            <?= $form->field($model, 'created_at')->staticField(date('d.m.Y H:i', $model->created_at)) ?>
            <?= $form->field($model, 'updated_at')->staticField(date('d.m.Y H:i', $model->updated_at)) ?>
        </div>
        <div class="tab-pane" id="seoTab">
            <?php echo $form->field($model, 'meta_title')->textInput(['maxlength' => true])->translatable() ?>
            <?php echo $form->field($model, 'meta_keywords')->textInput(['maxlength' => true])->translatable() ?>
            <?php echo $form->field($model, 'meta_description')->textInput(['maxlength' => true])->translatable() ?>
        </div>
        <div class="tab-pane" id="fields">
            <?php if (!$model->isNewRecord) { ?>
                <?php if ($fieldPanel = \app\modules\field\widgets\Choice::widget(['model' => $model])) { ?>
                    <?= $fieldPanel; ?>
                <?php } else { ?>
                    <?= Yii::t('field', 'The fields are not set. Ask can <a href="{url}">here</a>', ['url' => \yii\helpers\Url::to(['/admin/field/field/index'])]) ?>
                <?php } ?>
            <?php } else {?>
                <?= Yii::t('content', 'First you need to save the item, and then the form appears with relation fields') ?>
            <?php } ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
