<?php

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 06.04.2016
 * Project: oakcms
 * File name: index.php
 */
?>

<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow">
                <i class="ion ion-ios-people-outline"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('admin', 'New Members') ?></span>
                <span class="info-box-number">1</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>
<div class="row">
    <div class="container-fluid">
        <?= Yii::t('admin', 'Shortcode'); ?><br>
        <b>[widgetkit id='1']</b><br>
        <b>[block id='1']</b><br>
        <b>[block position='header']</b>
    </div>
</div>
