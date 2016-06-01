<?php

use app\modules\admin\widgets\ActiveForm;
use app\modules\admin\widgets\Button;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentArticles */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
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

            <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?php echo $form->field($model, 'slug')
                    ->hint(Yii::t('backend', 'If you\'ll leave this field empty, slug will be generated automatically'))
                    ->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'content')->widget(\app\widgets\Editor::className()) ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
                <?= $form->field($model, 'create_user_id')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'update_user_id')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'published_at')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'created_at')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'updated_at')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'create_user_ip')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
                <?= $form->field($model, 'comment_status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_3">
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
