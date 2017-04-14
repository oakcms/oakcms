<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * @var $this yii\web\View
 * @var $model \app\modules\user\models\User
 * @var $form yii\bootstrap\ActiveForm
 */

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

$this->title = Yii::t('backend', 'Edit account');
$ga = new \Google\Authenticator\GoogleAuthenticator();

$this->params['actions_buttons'] = [
    [
        'label' => Yii::t('admin', 'Update'),
        'options' => [
            'form' => 'user-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
];

$js = '
jQuery(\'#user-googleauthenticator\').on("switchChange.bootstrapSwitch", function() {
    console.log($(this).is(":checked"));
    if($(this).is(":checked")) { 
        $("#googleAuthenticatorData").slideDown();
    } else {
        $("#googleAuthenticatorData").hide();
    }
});';
$this->registerJs($js, \yii\web\View::POS_END);
?>

<div class="user-profile-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'user-id',
        ],
    ]); ?>

    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'newPassword')->passwordInput() ?>
    <?= $form->field($model, 'newPasswordRepeat')->passwordInput() ?>
    <?= $form->field($model, 'googleAuthenticator')->widget(\oakcms\bootstrapswitch\Switcher::className()) ?>

    <div id="googleAuthenticatorData" style="display: <?= $model->googleAuthenticator ? 'block' : 'none' ?>;">
        <?= $form->field($model, 'googleAuthenticatorSecret')->staticField($model->googleAuthenticatorSecret) ?>

        <div class="form-group">
            <label class="col-md-3 control-label" for="user-googleauthenticatorsecret">Google Authenticator Account</label>
            <div class="col-md-9">
                <div class="form-control-static"><?= Yii::$app->user->identity->username.'@'.$_SERVER['HTTP_HOST'] ?></div>
            </div>
        </div>
        <div class="form-group ">
            <label class="col-md-3 control-label"><?= Yii::t('user', 'Google Authenticator RQ') ?></label>
            <div class="col-md-9">
                <div id="googleAuthenticator">
                    <h3 style="margin-top: 0"><a href="https://support.google.com/accounts/answer/1066447" target="_blank"><?= Yii::t('user', 'Install Google Authenticator') ?></a></h3>
                    <img src="<?= $ga->getUrl(Yii::$app->user->identity->username, $_SERVER['HTTP_HOST'], $model->googleAuthenticatorSecret)?>" alt="Google Authenticator">
                </div>
            </div>
        </div>
        <?= $form->field($model, 'googleAuthSecretCode')->textInput(['maxlength' => 6]) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
