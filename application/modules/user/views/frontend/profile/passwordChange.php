<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\bootstrap\ActiveForm;
use app\modules\user\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\modules\user\forms\frontend\PasswordChangeForm */

$this->title = Yii::t('module', 'TITLE_PASSWORD_CHANGE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('module', 'TITLE_PROFILE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile-password-change">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'currentPassword')->passwordInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'newPassword')->passwordInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'newPasswordRepeat')->passwordInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('module', 'BUTTON_SAVE'), ['class' => 'btn btn-primary', 'name' => 'change-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
