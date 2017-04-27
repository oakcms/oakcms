<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: login.php
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this app\components\AdminView */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\modules\user\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

$this->bodyClass = 'hold-transition login-page';

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

    .user-login {
        min-height: 100vh;
    }

    .user-login .login-bg {
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        min-height: 100vh;
    }

    /*.login-logo {
        position: absolute;
        top: 0.5em;
        left: 0.5em;
    }*/

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
            <div class="login-container">
                <div class="login-box">
                    <div class="login-box-body">
                        <div class="login-logo">
                            <img src="/img/logo.svg" width="100" height="100">
                            <b>Oak</b>CMS
                        </div>
                        <p class="login-box-msg"><?= Yii::t('admin', '') ?></p>

                        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                        <?= $form->field($model, 'username', [
                            'template' => "<div class=\"form-group has-feedback\">{input}<span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>\n{hint}\n{error}</div>",
                            'labelOptions' => ['class' => 'control-label control-label-d4w col-md-4 text-right']
                        ])->textInput(['placeholder'=>Yii::t('admin', 'Username')])->label(false) ?>

                        <?= $form->field($model, 'password', [
                            'template' => "<div class=\"form-group has-feedback\">{input}<span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>\n{hint}\n{error}</div>",
                            'labelOptions' => ['class' => 'control-label control-label-d4w col-md-4 text-right']
                        ])->passwordInput(['placeholder'=>Yii::t('admin', 'Password')])->label(false) ?>

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="checkbox icheck">
                                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block btn-flat', 'type' => 'submit']) ?>
                            </div>
                            <!-- /.col -->
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <!-- /.login-box-body -->
                </div>
                <div class="login-footer">
                    <div class="bs-reset">
                        <div class="col-xs-12 bs-reset">
                            <div class="login-copyright">
                                <?= Yii::t('admin', 'Copyright &copy; 2015-{year} Hrivinskiy Vladumur. <a href="http://hryvinskyi.com/" target="_blank">hryvinskyi.com</a> <a href="http://www.design4web.biz/" target="_blank">design4web.biz</a>. All rights reserved.', ['year' => date('Y')]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.login-box -->
