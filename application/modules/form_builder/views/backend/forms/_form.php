<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;
use app\modules\form_builder\models\FormBuilderField;

/* @var $this yii\web\View */
/* @var $model app\modules\form_builder\models\FormBuilderForms */
/* @var $form yii\widgets\ActiveForm */

\app\modules\form_builder\assets\BackendAssets::register($this);

$fields = FormBuilderField::getJson($model->id);
$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('form_builder', 'Create') : Yii::t('form_builder', 'Update'),
        'options' => [
            'form' => 'form-builder-forms-id',
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
        'label' => Yii::t('form_builder', 'Save & Continue Edit'),
        'options' => [
            'onclick' => 'sendFormReload("#form-builder-forms-id")',
        ],
        'icon' => 'fa fa-check-circle',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
];
?>

<div class="form-builder-forms-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id'=>'form-builder-forms-id',
        ],
    ]); ?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab_1" data-toggle="tab" aria-expanded="true">
                    <?= Yii::t('form_builder', 'Common') ?>
                </a>
            </li>
            <li>
                <a href="#tab_2" data-toggle="tab" aria-expanded="false">
                    <?= Yii::t('form_builder', 'Fields') ?>
                </a>
            </li>
            <li>
                <a href="#tab_3" data-toggle="tab" aria-expanded="false">
                    <?= Yii::t('form_builder', 'Submission') ?>
                </a>
            </li>
            <li>
                <a href="#tab_4" data-toggle="tab" aria-expanded="false">
                    <?= Yii::t('form_builder', 'Email') ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'slug')
                                ->hint(Yii::t('admin', 'If you\'ll leave this field empty, slug will be generated automatically')) ?>
                            <?= $form->field($model, 'status')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>
                        </div>
                        <div class="col-sm-3">
                            <h3><?= Yii::t('form_builder', 'Plugin code') ?></h3>
                            <p><?= Yii::t('form_builder', 'Add this code to any content to show the form.') ?></p>
                            <code>
                                <?php if($model->isNewRecord): ?>
                                [form_builder id=""]
                                <?php else: ?>
                                [form_builder id="<?= $model->id ?>"]
                                <?php endif; ?>
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_2">
                <?php if($model->isNewRecord): ?>
                    <div class="callout callout-info">
                        <p><?= Yii::t('form_builder', 'Save form before adding fields.') ?></p>
                    </div>
                <?php else: ?>
                    <div class="build-wrap"
                         data-url="<?= \yii\helpers\Url::to(['update-fields', 'form_id' => $model->id]) ?>"></div>
                <?php endif; ?>
            </div>
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
    var formBuilderData = '<?= $fields ?>';
</script>
<style>
    .view-data.btn.btn-default {
        display: none;
    }
</style>
