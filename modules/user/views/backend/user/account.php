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

$js = "

";
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
            //'enctype' => 'multipart/form-data'
        ],
    ]); ?>
    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'password_confirm')->passwordInput() ?>
    <?= $form->field($model, 'googleAuthenticator')->checkbox(['class'=>'make-switch', 'data-size'=>'small'], false) ?>

    <div id="googleAuthenticator">
        <img src="<?= $ga->getUrl(Yii::$app->user->identity->email, $_SERVER['HTTP_HOST'], $ga->generateSecret()) ?>" alt="Google Authenticator">
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(document).ready(function () {

    });
</script>
