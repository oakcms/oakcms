<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\text\models\Text;
 */
?>

<div class="block_phone_fixed">
    <a href="<?= $model->getSetting('phoneLink') ?>" class="open_modal"><?= $model->getSetting('phone') ?></a>
</div>
