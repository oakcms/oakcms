<?php

use yii\helpers\Url;
use app\modules\admin\widgets\Html;
use app\modules\admin\widgets\ActiveForm;
use app\modules\admin\widgets\Button;
use app\modules\user\models\User;
use app\modules\language\models\Language;
use app\templates\backend\base\assets\BaseAsset;

$asset = BaseAsset::register($this);
\app\templates\frontend\base\assets\FancyboxAsset::register($this);

$this->registerJsFile($asset->baseUrl.'/js/photos.js', ['depends' => 'app\templates\backend\base\assets\BaseAsset']);
$photoTemplate = '
<tr data-id="{{photo_id}}">
    <td>
        <a href="{{photo_image}}" class="fancybox-button plugin-box" title="{{photo_title}}" data-rel="fancybox-button">
            <img class="photo-thumb" width="100" id="photo-{{photo_id}}" src="{{photo_thumb}}">
        </a>
    </td>
    <td>
        <div class="col-xs-9">
            <div class="input-group">
                <input class="form-control photo-file-title" value="{{photo_title}}">
                <span class="input-group-btn">
                    <a href="' . Url::to(['/admin/media/file-title/{{photo_id}}']) . '" class="btn btn-primary disabled save-file-title"><i class="fa fa-arrow-left fa-fw"></i> '. Yii::t('admin', 'Save') .'</a>
                </span>
            </div>
        </div>
    </td>
    <td>
        <input {{checked}} class="make-switch" data-size="small" type="radio" name="ContentArticles[main_image]" value="{{photo_id}}">
    </td>
    <td class="control vtop">
        <div class="btn-group" role="group">
            <a href="' . Url::to(['/admin/media/image/{{photo_id}}']) . '" class="change-image-button btn green" style="margin-right:0" title="'. Yii::t('admin', 'Change image') .'"><span class="fa fa-edit"></span></a>
            <a href="' . Url::to(['/admin/media/delete/{{photo_id}}?type=article']) . '" class="btn red" title="'. Yii::t('admin', 'Delete item') .'"><span class="fa fa-trash-o"></span></a>
            <input type="file" name="Medias[image]" class="change-image-input hidden">
        </div>
    </td>
</tr>
';

$photoTemplate = \rmrevin\yii\minify\HtmlCompressor::compress($photoTemplate, ['extra' => true]);
$this->registerJs("var photoTemplate = '{$photoTemplate}'", \yii\web\View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentArticles */
/* @var $form ActiveForm */
/* @var $lang \app\modules\system\models\Language */

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

<div class="content-articles-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'content-articles-id',
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
                <a href="#imagesTab" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-camera"></i> <?= Yii::t('content', 'Images') ?>
                </a>
            </li>
            <li class="">
                <a href="#seoTab" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-star"></i> <?= Yii::t('content', 'SEO') ?>
                </a>
            </li>
            <li class="">
                <a href="#category-fields" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-plus-square"></i> <?= Yii::t('content', 'Relation Fields') ?>
                </a>
            </li>
            <li class="">
                <a href="#tab_3" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-gear"></i> <?= Yii::t('content', 'Settings') ?>
                </a>
            </li>

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
                <?= $form->field($model, 'category_id')->dropDownList(\yii\helpers\ArrayHelper::map(
                    \app\modules\content\models\ContentCategory::find()->published()->all(), 'id', 'title'
                )) ?>

                <?= $form->field($model, 'tagNames')->widget(\dosamigos\selectize\SelectizeTextInput::className(), [

                    'loadUrl' => ['/admin/content/article/list'],
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'plugins' => ['remove_button'],
                        'valueField' => 'name',
                        'labelField' => 'name',
                        'searchField' => ['name'],
                        'create' => true,
                    ],
                ])->hint(Yii::t('content', 'Use commas to separate tags')) ?>

                <?= $form->field($model, 'description')->widget(\app\widgets\Editor::className())->translatable() ?>

                <?= $form->field($model, 'content')->widget(\app\widgets\Editor::className())->translatable() ?>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-9">
                            <?= Html::img($model->getUploadUrl('image')?:'/uploads/user/non_image.png', ['class' => 'img-thumbnail', 'style'=>'max-width:300px']) ?>
                        </div>
                    </div>
                </div>

                <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>
                <?= $form->field($model, 'layout')->dropDownList($layouts) ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
                <?php

                $user_id = ($model->isNewRecord) ? Yii::$app->user->identity->getId() : $model->create_user_id;
                echo $form->field($model, 'create_user_id')->staticField(User::findIdentity($user_id)->username)->label(Yii::t('content', 'Create User'))
                ?>
                <?= $form->field($model, 'update_user_id')->staticField(User::findIdentity($user_id)->username)->label(Yii::t('content', 'Update User')) ?>
                <?= $form->field($model, 'published_at')->widget(\oakcms\datetimepicker\DateTime::className()); ?>
                <?= $form->field($model, 'created_at')->staticField(date('d.m.Y H:i', $model->created_at)) ?>
                <?= $form->field($model, 'updated_at')->staticField(date('d.m.Y H:i', $model->updated_at)) ?>
                <?= $form->field($model, 'create_user_ip')->staticField() ?>
                <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
                <?= $form->field($model, 'comment_status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_3">
                <?foreach ($model->settings as $key=>$setting):?>
                    <?= Html::settingField($key, $setting, 'content') ?>
                <?endforeach;?>
                <? //= $form->field($model, 'access_type')->textInput() ?>
                <? //= $form->field($model, 'category_id')->textInput() ?>
            </div>

            <div class="tab-pane" id="imagesTab">
                <?if(!$model->isNewRecord):?>
                    <button id="photo-upload" class="btn btn-success text-uppercase" type="button">
                        <span class="glyphicon glyphicon-arrow-up"></span> <?= Yii::t('admin', 'Upload Images')?>
                    </button>
                    <small id="uploading-text" class="smooth" style="display: none"><?= Yii::t('admin', 'Uploading. Please wait')?><span></span></small>

                    <table id="photo-table" class="table table-bordered table-hover" style="margin-top:20px;display: <?= count($model->medias) ? 'table' : 'none' ?>;">
                        <thead>
                        <tr>
                            <th width="100"><?= Yii::t('admin', 'Image') ?></th>
                            <th><?= Yii::t('admin', 'Title') ?></th>
                            <th><?= Yii::t('admin', 'The main') ?></th>
                            <th width="115"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($model->medias as $photo) : ?>
                            <?= str_replace(
                                [
                                    '{{photo_id}}',
                                    '{{photo_thumb}}',
                                    '{{photo_image}}',
                                    '{{photo_title}}',
                                    '{{checked}}',
                                ],
                                [
                                    $photo->media_id,
                                    $photo->thumbImage,
                                    $photo->bigImage,
                                    $photo->file_title,
                                    ($model->main_image == $photo->media_id)?'checked':''
                                ],
                                $photoTemplate)
                            ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="empty" style="display: <?= count($model->medias) ? 'none' : 'block' ?>;"><?= Yii::t('admin', 'No photos uploaded yet') ?>.</p>
                    <div class="divform" data-url="<?=Url::to(['/admin/media/upload', 'id'=>$model->id, 'type'=>'article'])?>">
                        <?= Html::fileInput('', null, [
                            'id' => 'photo-file',
                            'class' => 'hidden',
                            'multiple' => 'multiple',
                        ])
                        ?>
                    </div>
                <?else:?>
                    <?= Yii::t('admin', 'First you need to save the item, and then the form appears with images')?>
                <?endif?>
            </div>
            <div class="tab-pane" id="seoTab">
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
