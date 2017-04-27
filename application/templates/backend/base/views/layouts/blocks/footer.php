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
 * Date: 09.10.2016
 * Project: osnovasite
 * File name: footer.php
 */
?>
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b><?= Yii::t('system', 'Version') ?>:</b>
        <?= \app\modules\system\Module::VERSION ?>
    </div>
    <?php echo Yii::t('admin', 'Copyright &copy; 2015-{year} Hrivinskiy Vladumur. <a href="http://hryvinskyi.com/" target="_blank">hryvinskyi.com</a> <a href="http://www.design4web.biz/" target="_blank">design4web.biz</a>. All rights reserved.', ['year' => date('Y')]) ?>
</footer>
