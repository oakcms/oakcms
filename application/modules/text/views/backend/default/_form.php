<?php

use app\modules\admin\widgets\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;
use app\modules\language\models\Language;
use app\templates\backend\base\assets\BaseAsset;

$asset = BaseAsset::register($this);

/**
 * @var $this yii\web\View
 * @var $model app\modules\text\models\Text
 * @var $form yii\widgets\ActiveForm
 * @var $menus \app\modules\menu\models\MenuType
 */

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
        'label' => $model->isNewRecord ? Yii::t('carousel', 'Create') : Yii::t('carousel', 'Update'),
        'options' => [
            'form' => 'text-id',
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
        'label' => Yii::t('carousel', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#text-id")',
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

<div class="text-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'text-id',
        ],
    ]); ?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab_1" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-file-text-o"></i> <?= Yii::t('text', 'content') ?>
                </a>
            </li>
            <li>
                <a href="#tab_2" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-file-text-o"></i> <?= Yii::t('text', 'Binding to the menu') ?>
                </a>
            </li>
            <li class="">
                <a href="#tab_3" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-gear"></i> <?= Yii::t('admin', 'Settings') ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <?php echo $form->field($model, 'title')->textInput(['maxlength' => true])->translatable() ?>

                <?= $form->field($model, 'subtitle')->textInput(['maxlength' => true])->translatable() ?>

                <?= $form->field($model, 'slug')->dropDownList($positions) ?>

                <?= $form->field($model, 'layout')->dropDownList(\yii\helpers\ArrayHelper::map($layouts, 'name', 'title')) ?>
                <div class="col-md-5 col-md-offset-3">
                    <div id="preview_image"></div>
                </div>
                <div class="clearfix"></div>
                <br>
                <br>
                <?= $form->field($model, 'text')->widget(\app\widgets\Editor::className())->translatable() ?>
            </div>
            <div class="tab-pane" id="tab_2">
                <?= $form->field($model, 'where_to_place')->dropDownList(\app\modules\text\models\Text::getWereToPlace()) ?>

                <div class="form-group field-text-links">
                    <label class="col-md-3 control-label" for="text-links"><?= Yii::t('text', 'Links') ?></label>
                    <div class="col-md-9">
                        <div class="checkbox well well-sm">
                            <div class="container-fluid">
                                <div class="row">
                                    <?php $i = 0; ?>
                                    <?php foreach($menus as $menu):?>
                                        <div class="col-xs-12 col-sm-6">
                                            <b><?= $menu->title ?></b><br>
                                            <?php foreach($menu->items as $cat) : ?>
                                                <label style="padding-left:  <?= $cat->level * 20 ?>px;">
                                                    <input type="checkbox" name="Text[links][]" <?=($model->links !== null && in_array($cat->id, $model->links))?"checked":""?> value="<?= $cat->id ?>">
                                                    <?= $cat->title ?>
                                                </label>
                                                <br>
                                            <?php endforeach;?>
                                        </div>
                                        <?= $i % 2  ? '<div class="clearfix"></div>' : '' ?>
                                        <?php $i++; ?>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </div>
                        <div class="hint-block"><?= Yii::t('text', 'Links will be posted where') ?></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_3">

            </div>
    <?php ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }

    $(document).ready(function () {
        function selLayout() {
            var scriptUrl = '<?= \yii\helpers\Url::to(['/admin/text/default/get-layout']) ?>/'+$("#text-layout option:selected").val();
            $.ajax({
                url: scriptUrl,
                type: 'get',
                dataType: 'json',
                async: false,
                success: function(data) {
                    var previewImage = $('<img src="'+data.preview_image+'" alt="" class="img-responsive">');
                    $('#preview_image').html(previewImage);
                }
            });

            <?php if($model->isNewRecord): ?>
            var scriptUrl2 = '<?= \yii\helpers\Url::to(['/admin/text/default/get-settings']) ?>/' + $("#text-layout option:selected").val();
            <?php else: ?>
            var scriptUrl2 = '<?= \yii\helpers\Url::to(['/admin/text/default/get-settings']) ?>/' + $("#text-layout option:selected").val() + '?id=<?= $model->id ?>'+'&lang=<?= $model->language ?>';
            <?php endif; ?>
            $.ajax({
                url: scriptUrl2,
                type: 'get',
                dataType: 'html',
                async: false,
                success: function(data) {
                    $('#tab_3').html(data);
                }
            });

        }
        selLayout();
        $("#text-layout").change(function () {
            selLayout();
        });
    });
</script>
