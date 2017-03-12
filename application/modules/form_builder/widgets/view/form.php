<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

/**
 * @var $model
 */

$form = \kartik\form\ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['/form_builder/form/view', 'slug'=>$model->slug])
]);
?>
    <?= $model->renderForm($form); ?>
<?php
\kartik\form\ActiveForm::end();
