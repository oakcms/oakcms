<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\bootstrap\Html;
use app\modules\admin\widgets\ActiveForm;
use app\modules\admin\widgets\Button;
use app\modules\user\models\UserProfile;

/**
 * @var $model UserProfile
 */

$this->title = Yii::t('user', 'User Profile');
$this->params['title_icon'] = 'fa fa-user';

$role = current(\Yii::$app->authManager->getRolesByUser($model->user->id));
$this->title = empty($model->name) ? Html::encode($model->user->username) : Html::encode($model->name);

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
<?php $form = ActiveForm::begin([
    'options' => [
        'id' => 'user-id',
        'enctype' => 'multipart/form-data'
    ],
]); ?>
<style>
    .field-userprofile-avatar {
        text-align: center;
    }
    .field-userprofile-avatar .file-input .file-preview-frame,
    .field-userprofile-avatar .file-input .file-preview-frame:hover {
        margin: 0;
        padding: 0;
        border: none;
        box-shadow: none;
        text-align: center;
    }
    .field-userprofile-avatar .file-input {
        display: table-cell;
        max-width: 220px;
    }
</style>
<?= $form->field($model, 'avatar')->widget(\kartik\widgets\FileInput::className(), [
    'options' => [
        'class' => 'kv-avatar center-block'
    ],
    'pluginOptions' => [
        'overwriteInitial' => true,
        'showCaption' => false,
        'showClose' => false,
        'browseIcon' => '<i class="glyphicon glyphicon-folder-open"></i>',
        'removeIcon' => '<i class="glyphicon glyphicon-remove"></i>',
        'layoutTemplates' => ['main2' => '{preview}{remove}{browse}'],
        'browseLabel' => '',
        'removeLabel' => '',
        'removeTitle' => Yii::t('user', 'Cancel or reset changes'),
        'allowedFileExtensions' => ["jpg", "png", "gif"],
        'defaultPreviewContent' => Html::img($model->getThumbUploadUrl('avatar'))
    ],
])->label('') ?>
<?= $form->field($model, 'firstname')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'middlename')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'lastname')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'locale')->dropDownlist(\yii\helpers\ArrayHelper::map(\app\modules\language\models\Language::getLanguages(), 'language_id', 'name')) ?>
<?= $form->field($model, 'gender')->dropDownlist([
    UserProfile::GENDER_FEMALE => Yii::t('admin', 'Female'),
    UserProfile::GENDER_MALE   => Yii::t('admin', 'Male'),
]) ?>

<?php ActiveForm::end(); ?>
