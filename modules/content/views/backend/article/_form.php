<?php

use app\modules\admin\widgets\Html;
use app\modules\admin\widgets\ActiveForm;
use app\modules\admin\widgets\Button;
use app\modules\user\models\User;
use app\modules\language\models\Language;
use app\templates\backend\base\assets\BaseAsset;

$asset = BaseAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentArticles */
/* @var $form ActiveForm */
/* @var $lang \app\modules\system\models\Language */

// Language
if($model->isNewRecord) {
    $languageBtn = [
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
    $languageBtn = [];
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
                <?= $form->field($model, 'content')->widget(\app\widgets\Editor::className())->translatable() ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
                <?= $form->field($model, 'create_user_id')->staticField(User::findIdentity($model->create_user_id)->username)->label(Yii::t('content', 'Create User')) ?>
                <?= $form->field($model, 'update_user_id')->staticField(User::findIdentity($model->update_user_id)->username)->label(Yii::t('content', 'Update User')) ?>
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
