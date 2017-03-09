<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 09.10.2016
 * Project: osnovasite
 * File name: lock-screen.php
 */
use app\modules\admin\widgets\ActiveForm;
use app\modules\admin\widgets\Html;

$this->bodyClass = ['hold-transition login-page'];

$file = file_get_contents("http://www.bing.com/HPImageArchive.aspx?format=xml&idx=0&n=10&mkt=en-US");
$images = simplexml_load_string($file);

$bundle = \app\templates\backend\base\assets\BaseAsset::register($this);
$js = "$('.login-bg').backstretch(loginImages,{fade: 1000,duration: 8000});";
$this->registerJsFile($bundle->baseUrl.'/js/jquery.backstretch.min.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJs($js, \yii\web\View::POS_END, 'backstretch');
?>
<script>
    var loginImages = [
        <?php foreach($images->image as $image):?>
        "http://www.bing.com<?= $image->url ?>",
        <?php endforeach?>
    ];
</script>
<style>
    .backstretch {
        opacity:0.8;
        -moz-opacity:0.8;
        filter: alpha(opacity=80) black;
        -khtml-opacity: 0.8;
        background-color:#000;
    }

    .skin-blue .wrapper {
        background-color: transparent;
    }

    .user-login {
        min-height: 100vh;
    }

    .user-login .login-bg {
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        min-height: 100vh;
    }

    .login-box-body,
    .register-box-body {
        background-color: transparent;
        border-radius: 10px;
    }

    .login-container {
        position: relative;
        min-height: 100vh;
    }

    .login-box, .register-box {
        max-width: 360px;
        width: auto;
        margin: 0 auto;
        padding-top: 25%;
    }

    .user-login .login-container > .login-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding-bottom: 10px;
        text-align: center;
    }


    @media (max-width: 1023px) {
        .user-login,
        .user-login .login-bg,
        .user-login .login-container {
            min-height: 50vh;
        }

        .login-box, .register-box {
            padding-top: 60px;
        }
        .user-login .login-container > .login-footer {
            position: relative;
            margin-top: 40px;
            padding-bottom: 0;
        }
    }
</style>

<div class="user-login">
    <div class="row bs-reset">
        <div class="col-md-8 bs-reset">
            <div class="login-bg">

            </div>
        </div>

        <div class="col-md-4 bs-reset">
            <div class="lockscreen-wrapper">
                <div class="login-logo">
                    <img src="<?= $bundle->baseUrl ?>/images/logo.svg" width="100" height="100">
                    <b>Oak</b>CMS
                </div>
                <!-- User name -->
                <div class="lockscreen-name text-center"><?= $user->publicIdentity ?></div>

                <!-- START LOCK SCREEN ITEM -->
                <div class="lockscreen-item">
                    <!-- lockscreen image -->
                    <div class="lockscreen-image">
                        <?php if($user->userProfile->avatar != ''):?>
                            <img class="img-circle" src="<?= $user->userProfile->getThumbUploadUrl('avatar') ?>" alt="<?= Yii::t('admin', 'Avatar image for {username}', ['username' => $user->username]) ?>">
                        <?php else:?>
                            <?= \cebe\gravatar\Gravatar::widget([
                                'email' => $user->email,
                                'size' => 160,
                                'options' => [
                                    'alt' => Yii::t('admin', 'Avatar image for {username}', ['username' => $user->username]),
                                    'class' => 'img-circle'
                                ]
                            ]); ?>
                        <?php endif?>
                    </div>
                    <!-- /.lockscreen-image -->

                    <!-- lockscreen credentials (contains the form) -->
                    <?php $form = ActiveForm::begin([
                        'action' => \yii\helpers\Url::to(['/admin/user/user/login']),
                        'options' => ['class' => 'lockscreen-credentials']
                    ]); ?>
                    <?= \app\modules\admin\widgets\Html::activeHiddenInput($model, 'username'); ?>
                        <div class="input-group">
                            <?= Html::activeInput('password', $model, 'password', ['placeholder' => Yii::t('admin', 'Password'), 'class' => 'form-control']); ?>

                            <div class="input-group-btn">
                                <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                            </div>
                        </div>
                    <?php ActiveForm::end(); ?>
                    <!-- /.lockscreen credentials -->
                </div>
                <!-- /.lockscreen-item -->
                <div class="help-block text-center">
                    <?= Yii::t('user', 'Enter your password to retrieve your session') ?>
                </div>
                <div class="text-center">
                    <a href="<?= \yii\helpers\Url::to(['/admin/user/user/login']); ?>"><?= Yii::t('user', 'Or sign in as a different user') ?></a>
                </div>
                <div class="lockscreen-footer text-center">
                    <?= Yii::t('admin', '2015-{year} &copy; Hrivinskiy Vladunur. <a href="http://codice.in.ua/" target="_blank">codice.in.ua</a> <a href="http://www.design4web.biz/" target="_blank">design4web.biz</a>. All rights reserved.', ['year'=>date('Y')]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.login-box -->
