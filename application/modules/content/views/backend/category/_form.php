<?php

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentCategory */
/* @var $form yii\widgets\ActiveForm */
$asset = \app\templates\backend\base\assets\BaseAsset::register($this);

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
    $allLang = \app\modules\language\models\Language::getLanguages();
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
            'form' => 'content-category-id',
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
            'onclick' => 'sendFormReload("#content-category-id")',
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

<div class="content-category-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'content-category-id',
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
                <a href="#tab_3" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-star"></i> <?= Yii::t('content', 'SEO') ?>
                </a>
            </li>
            <li class="">
                <a href="#category-fields" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-plus-square"></i> <?= Yii::t('content', 'Relation Fields') ?>
                </a>
            </li>

            <!--<li class="">
                <a href="#tab_4" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-gear"></i> <?/*= Yii::t('content', 'Settings') */?>
                </a>
            </li>-->

            <li class="pull-right">
                <a href="<?= \yii\helpers\Url::to(['/admin/modules/setting', 'name' => \Yii::$app->controller->module->id]) ?>" class="text-muted">
                    <i class="fa fa-gear"></i>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <?php echo $form->field($model, 'title')->textInput(['maxlength' => true])->translatable() ?>
                <?php echo $form->field($model, 'slug')
                    ->hint(Yii::t('admin', 'If you\'ll leave this field empty, slug will be generated automatically'))
                    ->textInput(['maxlength' => true])->translatable() ?>
                <?= (isset($parent)) ? $form->field($model, 'parent')->dropDownList(\yii\helpers\ArrayHelper::map(
                    \app\modules\content\models\ContentCategory::find()->andFilterWhere(['<>', 'id', $model->id])->all(), 'id', 'title'
                ), ['prompt' => '']) : '' ?>
                <?= $form->field($model, 'content')->widget(\app\widgets\Editor::className())->translatable() ?>
                <?= $form->field($model, 'layout')->dropDownList($layouts) ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
                <?= $form->field($model, 'created_at')->staticField(date('d.m.Y H:i', $model->created_at)) ?>
                <?= $form->field($model, 'updated_at')->staticField(date('d.m.Y H:i', $model->updated_at)) ?>
                <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
            </div>
            <div class="tab-pane" id="tab_3">
                <?= $form->field($model, 'meta_title')->textInput() ?>
                <?= $form->field($model, 'meta_keywords')->textInput() ?>
                <?= $form->field($model, 'meta_description')->textInput() ?>
            </div>
            <div class="tab-pane" id="category-fields">
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
            <!-- /.tab-pane -->
            <!--<div class="tab-pane" id="tab_5">
                <?/*foreach ($model->settings as $key=>$setting):*/?>
                    <?/*= Html::settingField($key, $setting, 'content') */?>
                <?/*endforeach;*/?>
            </div>-->
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
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
