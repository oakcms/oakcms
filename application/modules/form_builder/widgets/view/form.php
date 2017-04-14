<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * @var $model \app\modules\form_builder\models\FormBuilderForms
 */
$index = new \app\components\Count();
$form = \kartik\form\ActiveForm::begin([
    'id' => 'fb_form_id_'.$model->id.'_'.$index->getIndex(),
    'action' => \yii\helpers\Url::to(['/form_builder/form/view', 'slug'=>$model->slug]),
    'options' => ['class' => 'fb_form']
]);
?>
    <?= $model->renderForm($form); ?>
<?php
\kartik\form\ActiveForm::end();
