<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;
use app\modules\form_builder\models\FormBuilderField;
use app\modules\admin\widgets\AceEditor;

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
                    <?= Yii::t('form_builder', 'Design') ?>
                </a>
            </li>
            <li>
                <a href="#tab_4" data-toggle="tab" aria-expanded="false">
                    <?= Yii::t('form_builder', 'Submission') ?>
                </a>
            </li>
            <li>
                <a href="#tab_5" data-toggle="tab" aria-expanded="false">
                    <?= Yii::t('form_builder', 'Email') ?>
                </a>
            </li>
            <li class="pull-right">
                <a href="<?= Url::to(['/form_builder/form/view', 'slug' => $model->slug]) ?>"
                   target="_blank" class="text-muted"><i class="fa fa-external-link"></i></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="mb-20"></div>
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
                    <div id="renderFormBuilder"
                         class="hidden"
                         data-url="<?= Url::to(['get-form-html', 'form_id' => $model->id]) ?>"></div>
                    <div class="build-wrap"
                         data-url="<?= Url::to(['update-fields', 'form_id' => $model->id]) ?>">
                        <div class="table-responsive">
                            <?php \yii\widgets\Pjax::begin(['id' => 'pjax_form_fields']); ?>
                            <div style="height: 35px">
                                <div class="btn-group" id="grid_form_fields_buttons" style="display: none">
                                    <a href="#" type="button"
                                            id="grid_form_field_delete"
                                            class="btn btn-default btn-sm"
                                            data-url="<?= Url::to(['delete-fields-ids']) ?>"
                                    >
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </div>
                                <div class="pull-right">
                                    <?= \app\widgets\ModalIFrame::widget([
                                        'label'         => Yii::t('form_builder', 'Add Field'),
                                        'url'           => ['create-field', 'id' => $model->id],
                                        'dataHandler'   => '',
                                        'actionHandler' => '',
                                        'options'       => ['class' => 'btn btn-circle btn-primary btn-sm'],
                                        'popupOptions'  => ['width' => 600]
                                    ]); ?>
                                </div>
                            </div>
                            <?= \yii\grid\GridView::widget([
                                'id' => 'grid_form_fields',
                                'showOnEmpty' => false,
                                'showHeader' => false,
                                'layout' => "{items}\n{pager}",
                                'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
                                'dataProvider' => $dataProviderField,
                                'options' => [
                                    'class' => 'grid-view',
                                    'data' => [
                                        'sortable-widget' => 1,
                                        'sortable-url' => Url::toRoute(['sorting-fields']),
                                    ],
                                ],
                                'rowOptions' => function ($model, $key, $index, $grid) {
                                    return ['data-sortable-id' => $model->id];
                                },
                                'columns' => [
                                    [
                                        'class' => \kotchuprik\sortable\grid\Column::className(),
                                        'options' => ['style' => 'width:30px']
                                    ],
                                    [
                                        'class' => 'yii\grid\CheckboxColumn',
                                        'options' => ['style' => 'width:36px']
                                    ],
                                    [
                                        'attribute' => 'label',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            /** @var $model FormBuilderField */

                                            return \app\widgets\ModalIFrame::widget([
                                                'label'         => strip_tags($model->label),
                                                'url'           => ['update-field', 'id' => $model->id],
                                                'dataHandler'   => '',
                                                'actionHandler' => '',
                                                'popupOptions'  => ['width' => 600]
                                            ]);
                                        }
                                    ],
                                    'slug',
                                    'type',
                                ],
                            ]); ?>
                            <?php \yii\widgets\Pjax::end(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane" id="tab_3">
                <?php if($model->isNewRecord): ?>
                    <div class="callout callout-info">
                        <p><?= Yii::t('form_builder', 'Save form before adding design.') ?></p>
                    </div>
                <?php else: ?>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab_template_html" data-toggle="tab" aria-expanded="false">
                                <?= Yii::t('form_builder', 'HTML Template') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#tab_template_css-javascript" data-toggle="tab" aria-expanded="false">
                                <?= Yii::t('form_builder', 'CSS and Javascript') ?>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_template_html">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div class="mb-20"></div>
                                        <div class="form-group">
                                            <label class="col-sm-3"><?= Yii::t('form_builder', 'Enable Edit') ?></label>
                                            <div class="col-sm-9">
                                                <?= Html::hiddenInput('design[edit_html]', '0') ?>
                                                <?= \oakcms\bootstrapswitch\Switcher::widget([
                                                    'id' => 'switcher_edit_html',
                                                    'name' => 'design[edit_html]',
                                                    'checked' => ArrayHelper::getValue($model->data, 'design.edit_html')
                                                ]); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label><?= Yii::t('form_builder', 'Html') ?></label>

                                            <?= AceEditor::widget([
                                                'name'     => 'design[html]',
                                                'mode'     => 'html',
                                                'readOnly' => !ArrayHelper::getValue($model->data, 'design.edit_html'),
                                                'id'       => 'design_html',
                                                'value'    => ArrayHelper::getValue($model->data, 'design.html', '')
                                            ]) ?>
                                            <div class="hint-block">
                                                <?= Yii::t('form_builder', 'You can add your Javascript declarations here. Do not add &lt;script&gt; tags.') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <h3><?= Yii::t('form_builder', 'Available variables') ?></h3>
                                        <div id="template_variables" data-url="<?= Url::to(['get-template-variables', 'form_id' => $model->id]) ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_template_css-javascript">
                            <div class="form-group">
                                <label><?= Yii::t('form_builder', 'Custom CSS') ?></label>

                                <?= AceEditor::widget([
                                    'name'  => 'design[css]',
                                    'mode'  => 'css',
                                    'id'    => 'design_css',
                                    'value' => ArrayHelper::getValue($model->data, 'design.css', '')
                                ]) ?>
                                <div class="hint-block">
                                    <?= Yii::t('form_builder', 'You can add your CSS declarations here. Do not add &lt;style&gt; tags.') ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><?= Yii::t('form_builder', 'Custom javascript') ?></label>

                                <?= AceEditor::widget([
                                    'name'  => 'design[javascript]',
                                    'mode'  => 'javascript',
                                    'id'    => 'design_javascript',
                                    'value' => ArrayHelper::getValue($model->data, 'design.javascript', '')
                                ]) ?>
                                <div class="hint-block">
                                    <?= Yii::t('form_builder', 'You can add your Javascript declarations here. Do not add &lt;script&gt; tags.') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane" id="tab_4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="mb-20"></div>
                            <?= \app\modules\admin\widgets\Html::settingField(
                                'after_submit',
                                [
                                    'type'  => 'dropDownList',
                                    'value' => ArrayHelper::getValue($model->data, 'submission.after_submit', ''),
                                    'items' => [
                                        'thankyou' => Yii::t('form_builder', 'Show Thank you message'),
                                        'redirect' => Yii::t('form_builder', 'Redirect to page'),
                                    ],
                                    'template' => "<div class='container-fluid'><div class='form-group'>{label}\n{element}\n{hint}</div></div>",
                                    'options' => [
                                        'labelOptions' => ['class' => 'control-label']
                                    ]
                                ],
                                'form_builder',
                                'submission'
                            ) ?>

                            <?= \app\modules\admin\widgets\Html::settingField(
                                'after_submit_link',
                                [
                                    'type'  => 'textInput',
                                    'value' => ArrayHelper::getValue($model->data, 'submission.after_submit_link', ''),
                                    'template' => "<div class='container-fluid'><div class='form-group'>{label}\n{element}\n{hint}</div></div>",
                                    'options' => [
                                        'labelOptions' => ['class' => 'control-label']
                                    ]
                                ],
                                'form_builder',
                                'submission'
                            ) ?>

                            <?= \app\widgets\Editor::widget([
                                'name'     => 'submission[content]',
                                'value'    => ArrayHelper::getValue($model->data, 'submission.content', '')
                            ]); ?>

                        </div>
                        <div class="col-sm-3">
                            <h3><?= Yii::t('form_builder', 'Available variables') ?></h3>
                            <div class="js-submission_variables" data-url="<?= Url::to(['get-submission-variables', 'form_id' => $model->id]) ?>"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_5">
                <?php if($model->isNewRecord): ?>
                    <div class="callout callout-info">
                        <p><?= Yii::t('form_builder', 'Save form before adding email.') ?></p>
                    </div>
                <?php else: ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="mb-20"></div>
                            <div class="form-group">
                                <label class="col-md-3"><?= Yii::t('form_builder', 'Enable Send To User') ?></label>
                                <div class="col-sm-9">
                                    <?= Html::hiddenInput('email[sendToUser]', '0') ?>
                                    <?= \oakcms\bootstrapswitch\Switcher::widget([
                                        'name' => 'email[sendToUser]',
                                        'checked' => ArrayHelper::getValue($model->data, 'email.sendToUser')
                                    ]); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="container-fluid">
                                    <label><?= Yii::t('form_builder', 'User Email Field') ?></label>
                                    <div>
                                        <?= \kartik\select2\Select2::widget([
                                            'initValueText' => Yii::t('form_builder', 'Select a field'),
                                            'id'      => 'email_field_user',
                                            'name'    => 'email[userEmail]',
                                            'data'    => [],
                                            'value'   => ArrayHelper::getValue($model->data, 'email.userEmail'),
                                            'options' => [
                                                'data'=>[
                                                    'url' => Url::to(['get-email-user', 'form_id' => $model->id]),
                                                    'selected' => ArrayHelper::getValue($model->data, 'email.userEmail', 0)
                                                ]
                                            ],
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                            <?= \app\modules\admin\widgets\Html::settingField(
                                'userEmailSubject',
                                [
                                    'type' => 'textInput',
                                    'value' => ArrayHelper::getValue($model->data, 'email.userEmailSubject'),
                                    'template' => "<div class='container-fluid'><div class='form-group'>{label}\n{element}\n{hint}</div></div>",
                                    'options' => [
                                        'labelOptions' => ['class' => 'control-label']
                                    ]
                                ],
                                'form_builder',
                                'email'
                            ); ?>
                            <div class="form-group">
                                <div class="container-fluid">
                                    <label class="control-label"><?= Yii::t('form_builder', 'User Email Content') ?></label>
                                    <div>
                                        <?= \app\widgets\Editor::widget([
                                            'name'    => 'email[userEmailContent]',
                                            'value'   => ArrayHelper::getValue($model->data, 'email.userEmailContent'),
                                        ]); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3"><?= Yii::t('form_builder', 'Enable Send To Admin') ?></label>
                                <div class="col-sm-9">
                                    <?= Html::hiddenInput('email[sendToAdmin]', '0') ?>
                                    <?= \oakcms\bootstrapswitch\Switcher::widget([
                                        'name' => 'email[sendToAdmin]',
                                        'checked' => ArrayHelper::getValue($model->data, 'email.sendToAdmin')
                                    ]); ?>
                                </div>
                            </div>
                            <?= \app\modules\admin\widgets\Html::settingField(
                                'adminEmail',
                                [
                                    'type' => 'textInput',
                                    'value' => ArrayHelper::getValue($model->data, 'email.adminEmail'),
                                    'template' => "<div class='container-fluid'><div class='form-group'>{label}\n{element}\n{hint}</div></div>",
                                    'options' => [
                                        'labelOptions' => ['class' => 'control-label']
                                    ]
                                ],
                                'form_builder',
                                'email'
                            ); ?>
                            <?= \app\modules\admin\widgets\Html::settingField(
                                'adminEmailSubject',
                                [
                                    'type' => 'textInput',
                                    'value' => ArrayHelper::getValue($model->data, 'email.adminEmailSubject'),
                                    'template' => "<div class='container-fluid'><div class='form-group'>{label}\n{element}\n{hint}</div></div>",
                                    'options' => [
                                        'labelOptions' => ['class' => 'control-label']
                                    ]
                                ],
                                'form_builder',
                                'email'
                            ); ?>
                            <div class="form-group">
                                <div class="container-fluid">
                                    <label class="control-label"><?= Yii::t('form_builder', 'Admin Email Content') ?></label>
                                    <div>
                                        <?= \app\widgets\Editor::widget([
                                            'name'    => 'email[adminEmailContent]',
                                            'value'   => ArrayHelper::getValue($model->data, 'email.adminEmailContent'),
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h3><?= Yii::t('form_builder', 'Available variables') ?></h3>
                            <div class="js-submission_variables" data-url="<?= Url::to(['get-submission-variables', 'form_id' => $model->id]) ?>"></div>
                        </div>
                    </div>
                </div>
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
