<?php

use yii\helpers\Url;

/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 14.06.2016
 * Project: oakcms
 * File name: index.php
 */

?>

<div class="btn-group">
    <a href="<?= Url::to(['/admin/cache/flush-cache']) ?>" class="btn btn-default">
        <i class="glyphicon glyphicon-flash"></i>
        <?= Yii::t('admin', 'Flush cache') ?>
    </a>
    <a href="<?= Url::to(['/admin/cache/clear-assets']) ?>" class="btn btn-default">
        <i class="glyphicon glyphicon-trash"></i>
        <?= Yii::t('admin', 'Clear assets') ?>
    </a>
</div>
