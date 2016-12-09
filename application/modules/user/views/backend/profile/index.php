<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * @var $model \app\modules\user\models\UserProfile;
 * @var $this \app\components\CoreView;
 */

$this->title = Yii::t('user', 'User Profile');
$this->params['title_icon'] = 'fa fa-user';

$role = current(\Yii::$app->authManager->getRolesByUser($model->user->id));
?>

<div class="col-md-3">
    <div class="box box-primary">
        <div class="box-body box-profile">
            <?if($model->avatar != ''):?>
                <img class="profile-user-img img-responsive img-circle" src="<?= $model->getThumbUploadUrl('avatar') ?>" alt="<?= Yii::t('admin', 'Avatar image for {username}', ['username' => $model->user->username]) ?>">
            <?else:?>
                <?= \cebe\gravatar\Gravatar::widget([
                    'email' => $model->user->email,
                    'options' => [
                        'alt' => Yii::t('admin', 'Avatar image for {username}', ['username' => $model->user->username]),
                        'class' => 'profile-user-img img-responsive img-circle'
                    ]
                ]); ?>
            <?endif?>

            <h3 class="profile-username text-center"><?= $model->getFullName(); ?></h3>
            <p class="text-muted text-center"><?= $role->description; ?></p>
        </div>
    </div>
</div>

