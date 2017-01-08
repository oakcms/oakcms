<?php
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
    <?if($model->googleAuthenticator):?>
    <div class="form-group">
        <label class="col-md-3 control-label"><?= Yii::t('user', 'Google Authenticator RQ') ?></label>
        <div class="col-md-9">
            <div id="googleAuthenticator">
                <h3 style="margin-top: 0"><a href="https://support.google.com/accounts/answer/1066447" target="_blank"><?= Yii::t('user', 'Install Google Authenticator') ?></a></h3>
                <img src="<?= $ga->getUrl(Yii::$app->user->identity->email, $_SERVER['HTTP_HOST'], $model->googleAuthenticatorSecret)?>" alt="Google Authenticator">
            </div>
        </div>
    </div>
    <?endif;?>
    <?php ActiveForm::end(); ?>

</div>
