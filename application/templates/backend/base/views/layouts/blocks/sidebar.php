<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 07.04.2016
 * Project: oakcms
 * File name: sidebar.php
 *
 * @var $this \app\components\AdminView;
 */


$userIdentity = Yii::$app->user->identity;

?>

<aside class="main-sidebar">
    <section class="sidebar" style="height: auto;">
<!--        <div class="user-panel">-->
<!--            <div class="pull-left image">-->
<!--                --><?//if($userIdentity->userProfile->avatar != ''):?>
<!--                    <img class="avatar" src="--><?//= $userIdentity->userProfile->getThumbUploadUrl('avatar') ?><!--" alt="--><?//= Yii::t('user', 'Avatar image for {username}', ['username' => $userIdentity->username]) ?><!--">-->
<!--                --><?//else:?>
<!--                    --><?//= \cebe\gravatar\Gravatar::widget([
//                        'email' => $userIdentity->email,
//                        'size' => 64,
//                        'options' => [
//                            'alt' => Yii::t('user', 'Avatar image for {username}', ['username' => $userIdentity->username]),
//                            'class' => 'avatar'
//                        ]
//                    ]); ?>
<!--                --><?//endif?>
<!--            </div>-->
<!--            <div class="pull-left info">-->
<!--                <p class="fs-13">--><?//= $userIdentity->publicIdentity ?><!--</p>-->
<!--                <span><i class="fa fa-circle text-success"></i> Online</span>-->
<!--            </div>-->
<!--        </div>-->
        <ul class="sidebar-menu">
            <li class="header"><?= Yii::t('admin', 'Main navigation') ?></li>
        </ul>
        <?php
        $menu[] = [
            'label' => Yii::t('admin', 'System'),
            'icon' => '<i class="fa fa-cogs" aria-hidden="true"></i>',
            'items' => [
                ['label' => Yii::t('admin', 'Settings'), 'url' => ['/admin/settings/index'], 'icon' => '<i class="fa fa-cog"></i>'],
                ['label' => Yii::t('admin', 'Modules'), 'url' => ['/admin/modules/index'], 'icon' => '<i class="fa fa-puzzle-piece"></i>'],
                ['label' => Yii::t('admin', 'Cache'), 'url' => ['/admin/cache/index'], 'icon' => '<i class="fa fa-flash"></i>'],
                ['label' => Yii::t('admin', 'File Manager'), 'url' => ['/admin/file-manager/index'], 'icon' => '<i class="fa fa-folder-open"></i>'],
            ]
        ];
        echo app\modules\admin\widgets\Menu::widget([
            'options' => [
                'class' => 'sidebar-menu',
                'data-keep-expanded' => 'false',
                'data-auto-scroll' => 'true',
                'data-slide-speed' => '200',
            ],
            'labelTemplate' => '<a href="javascript:;" class="menu-item">{icon}<span class="title">{label}</span>{right-icon}{badge}</a>',
            'linkTemplate' => '<a href="{url}" class="menu-item">{icon}<span class="title">{label}</span>{right-icon}{badge}</a>',
            'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
            'activateParents' => true,
            'customItems' => $menu
        ]) ?>
    </section>
    <!-- /.sidebar -->
</aside>
