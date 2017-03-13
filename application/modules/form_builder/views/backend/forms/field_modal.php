<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */


/**
 * @var $model \app\modules\form_builder\models\FormBuilderField;
 * @var $modelFormField \app\modules\form_builder\models\FormBuilder;
 * @var $fieldData array
 */
use \yii\helpers\ArrayHelper;
use \kartik\builder\Form;
use \kartik\form\ActiveForm;
use \kartik\alert\Alert;

$attributes = ArrayHelper::getValue($fieldData, 'attributes', []);
?>
<?php $form = ActiveForm::begin(); ?>
<div class="modal-header">
    <h3>
        <?= Yii::t('form_builder', 'Edit {fieldName} <span style="color: #f0615c">{fieldLabel}</span>', [
            'fieldName' => $fieldData['title'],
            'fieldLabel' => $model->label
        ]) ?>
    </h3>
</div>
<div class="modal-body">
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $type => $body) {
        echo Alert::widget([
            'type' => 'alert-'.$type,
            'body' => $body
        ]);
        ?>
        <script>
            $(document).ready(function () {
                window.parent.$.pjax.reload('#pjax_form_fields');
                window.parent.fieldsUpdateTemplate();
                window.parent.yii.gromverIframe.closePopup();
            });
        </script>
        <?php
    }
    ?>
    <div class="form">
        <?php echo Form::widget([
            'model' => $modelFormField,
            'form' => $form,
            'attributes' => $attributes
        ]); ?>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?= Yii::t('form_builder', 'Send') ?></button>
</div>
<?php ActiveForm::end(); ?>
