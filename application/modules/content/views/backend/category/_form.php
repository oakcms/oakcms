<?php

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\ContentCategory */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
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
            <!--<li class="">
                <a href="#tab_3" data-toggle="tab" aria-expanded="false">
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
                <?= $form->field($model, 'content')->widget(\app\widgets\Editor::className())->translatable() ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
                <?= $form->field($model, 'created_at')->staticField(date('d.m.Y H:i', $model->created_at)) ?>
                <?= $form->field($model, 'updated_at')->staticField(date('d.m.Y H:i', $model->updated_at)) ?>
                <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
            </div>
            <!-- /.tab-pane -->
            <!--<div class="tab-pane" id="tab_3">
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
