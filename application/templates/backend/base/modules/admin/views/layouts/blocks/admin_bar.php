<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 07.04.2016
 * Project: oakcms
 * File name: admin_bar.php
 *
 * @var $userIdentity \app\modules\user\models\User;
 * @var $this \app\components\CoreView;
 */

use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\assets\MediaSystem;
use app\assets\jQuerySimpleSwitch;

$asset = MediaSystem::register($this);
jQuerySimpleSwitch::register($this);
\app\assets\FontAwesome::register($this);

$js = "jQuery(document).ready(function() {
    var icb = $('#oak_admin_bar .simple-switch');
    icb.simpleSwitch();
    $('#oak_admin_bar').on('change', '.simple-switch', function(){
        var cb = $(this);
        cb.prop('disabled', true);
        location.href = cb.attr('data-url') + '/' + (cb.is(':checked') ? 1 : 0);
    });
});
";
$js .= '$(\'.oakcms-edit\').each(function(i, element){var $this = $(element);$this.append(\'<a href=\"\'+$this.data(\'edit\')+\'\" class=\"oakcms-goedit\" style=\"width: \'+$this.width()+\'px; height: \'+$this.height()+\'px;\" target=\"_blank\"></a>\');});';

$this->registerCss('body {padding-top: 30px;}');
$this->registerJS($js, View::POS_END, 'oak_admin_bar_js');
$this->registerJsFile($asset->baseUrl.'/js/admin_bar.js', ['depends' => [\yii\web\JqueryAsset::className()]], 'admin_bar_js_file');

$userIdentity = Yii::$app->user->identity;

// Якщо користувач має доступ до адмін панелі
if(\Yii::$app->user->can(\app\modules\admin\rbac\Rbac::PERMISSION_ADMIN_PANEL)) {
    $urlProfile = Url::to(['/admin/user/profile/index']);
} else {
    $urlProfile = Url::to(['/user/profile/index']);
}
?>
<div id="oak_admin_bar" class="">
    <a class="cms-logo" href="<?= Url::to(['/admin']) ?>" title="<?= Yii::t('admin', 'Go to admin panel') ?>" data-toggle="tooltip" data-placement="bottom">
        <img src="<?= $asset->baseUrl ?>/images/icon-logo.svg" alt="OakCMS">
        <?= Yii::t('admin', 'Admin panel') ?>
    </a>

    <div class="live-edit">
        <?= Html::checkbox('', LIVE_EDIT, ['class'=>'simple-switch','data-url' => Url::to(['/system/default/live-edit'])]) ?>
        <i class="glyphicon glyphicon-pencil"></i>
        <span><?= Yii::t('admin', 'Live edit') ?></span>
    </div>

    <ul class="menus-icon">
        <li>
            <a href="<?= Url::to(['/admin/cache/flush-cache']) ?>" class="js-oak-flush-cache" data-toggle="tooltip" title="<?= Yii::t('admin', 'Flush cache') ?>">
                <i class="glyphicon glyphicon-flash"></i>
            </a>
        </li>
        <li>
            <a href="<?= Url::to(['/admin/cache/clear-assets']) ?>" class="js-oak-clear-assets" data-toggle="tooltip" title="<?= Yii::t('admin', 'Clear assets') ?>">
                <i class="glyphicon glyphicon-trash"></i>
            </a>
        </li>
    </ul>

    <div class="user-menu">
        <a href="#"><?= $userIdentity->username ?></a>
        <div class="sub-wrapper">
            <ul class="submenu">
                <li>
                    <a href="<?= $urlProfile ?>">
                        <?= \cebe\gravatar\Gravatar::widget([
                            'email' => $userIdentity->email,
                            'size' => 64,
                            'options' => [
                                'alt' => Yii::t('admin', 'Avatar image for {username}', [
                                    'username' => $userIdentity->username
                                ]),
                                'class' => 'avatar'
                            ]
                        ]); ?>
                        <span class="full-name">
                        <?= $userIdentity->publicIdentity ?>
                            (<small class="username"><?= $userIdentity->username ?></small>)
                    </span>
                    </a>
                </li>
                <li>
                    <a href="<?= Url::to(['/user/default/logout']) ?>" data-method="post"><?= Yii::t('admin', 'Logout') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
