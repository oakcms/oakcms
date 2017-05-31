<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use app\modules\form_builder\components\ActiveForm;

/**
 * @var $model \app\modules\form_builder\models\FormBuilderForms
 * @var $formModel \app\modules\form_builder\models\FormBuilder
 */
$index = new \app\components\Count();
$form = ActiveForm::begin([
    'model'   => $formModel,
    'formId'  => $model->id,
    'id'      => 'fb_form_id_' . $model->id . '_' . $index->getIndex(),
    'action'  => \yii\helpers\Url::to(),
    'options' => ['class' => 'fb_form'],
]);
?>
<?= $model->renderForm($form); ?>
<?php
ActiveForm::end();
