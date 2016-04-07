<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 07.04.2016
 * Project: oakcms
 * File name: admin_bar.php
 */

use yii\helpers\Url;
use yii\bootstrap\Html;
use app\assets\MediaSystem;
use app\assets\jQuerySimpleSwitch;

jQuerySimpleSwitch::register($this);
$asset = MediaSystem::register($this);
$this->registerCss('body {padding-top: 50px;}');
?>
<div id="oak_admin_bar" class="">
    <a class="cms-logo" href="<?= Url::to(['/admin']) ?>" title="<?= Yii::t('app', 'Go to admin panel') ?>" data-toggle="tooltip" data-placement="bottom">
        <img src="<?= $asset->baseUrl ?>/images/icon-logo.svg" alt="OakCMS"> Admin panel
    </a>

    <div class="live-edit">
        <?= Html::checkbox('', LIVE_EDIT, ['class'=>'simple-switch','data-url' => Url::to(['/system/default/live-edit'])]) ?>
        <?= Yii::t('app', 'Live edit') ?>
        <i class="glyphicon glyphicon-pencil"></i>
    </div>
</div>
