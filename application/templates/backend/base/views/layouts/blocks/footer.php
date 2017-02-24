<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 09.10.2016
 * Project: osnovasite
 * File name: footer.php
 */
?>
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b><?= Yii::t('system', 'Version') ?></b> <?= \app\modules\system\Module::VERSION ?>
    </div>
    <strong>
        Copyright © 2015-<?= date('Y') ?> <a href="http://design4web.biz/" target="_blank">Design4web Studio</a>.
    </strong>
    <?= Yii::t('admin', '2015-{year} &copy; Hrivinskiy Vladunur. <a href="http://codice.in.ua/" target="_blank">codice.in.ua</a> <a href="http://www.design4web.biz/" target="_blank">design4web.biz</a>. All rights reserved.', ['year'=>date('Y')]) ?>
</footer>
