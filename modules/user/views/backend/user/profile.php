<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\admin\widgets\ActiveForm;
use app\modules\admin\widgets\Button;
use app\modules\user\models\UserProfile;
use app\modules\system\models\Language;

/* @var $this yii\web\View */
/* @var $model UserProfile */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Edit profile');

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'),
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
            'enctype' => 'multipart/form-data'
        ],
    ]); ?>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-9">
                <?= Html::img($model->getUploadUrl('avatar')?:'/uploads/user/non_image.png', ['class' => 'img-thumbnail', 'style'=>'max-width:300px']) ?>
            </div>
        </div>
    </div>
    <?= $form->field($model, 'avatar')->fileInput(['accept' => 'image/*']) ?>
    <?= $form->field($model, 'firstname')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'middlename')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'lastname')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'locale')->dropDownlist(ArrayHelper::map(Language::getLanguages(), 'language_id', 'name')) ?>
    <?= $form->field($model, 'gender')->dropDownlist([
        UserProfile::GENDER_FEMALE => Yii::t('backend', 'Female'),
        UserProfile::GENDER_MALE => Yii::t('backend', 'Male')
    ]) ?>
    <?php ActiveForm::end(); ?>
</div>
